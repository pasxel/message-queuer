<?php
/**
 * This file is part of the te/message-queuer package.
 *
 * (c) 2014 Turtle Entertainment GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TurtleEntertainment\MessageQueuer\Adapter;

use Aws\Sqs\SqsClient;
use TurtleEntertainment\MessageQueuer\Message;

/**
 * MQ adapter for Amazon SQS
 *
 * @author Christian Eikermann <cei@turtle-entertainment.com>
 */
class AmazonSqsAdapter extends AbstractAdapter
{
    /**
     * @var SqsClient
     */
    protected $sqsClient;

    /**
     * Constructor
     *
     * @param SqsClient $sqsClient
     */
    public function __construct(SqsClient $sqsClient)
    {
        $this->sqsClient = $sqsClient;
    }

    /**
     * {@inheritdoc}
     */
    protected function doReceiveMessages($queueId)
    {
        $response = $this->sqsClient->receiveMessage(array(
            'QueueUrl' => $queueId,
            'MaxNumberOfMessages' => 10,
        ));

        $result = $response->getPath('Messages');
        if (!is_array($result)) {
            return array();
        }

        $messages = array();
        foreach ($result as $data) {
            $message = new Message();
            $message->setId($data['MessageId']);
            $message->setQueueId($queueId);
            $message->setData($data['Body']);
            $message->setParameter('ReceiptHandle', $data['ReceiptHandle']);
            $messages[] = $message;
        }

        return $messages;
    }

    /**
     * {@inheritdoc}
     */
    protected function doSendMessages(array $messages)
    {
        /* @var array|Message[] $messages */
        $id = 1;

        $args = array();
        foreach ($messages as $message) {
            $args[$message->getQueueId()][] = array(
                'Id' => $id++,
                'MessageBody' => $message->getData(),
            );
        }

        foreach ($args as $queueId => $data) {
            $this->sqsClient->sendMessageBatch(array(
                'QueueUrl' => $queueId,
                'Entries' => $data,
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function doReleaseMessages(array $messages)
    {
        /* @var array|Message[] $messages */
        $id = 1;

        $args = array();
        foreach ($messages as $message) {
            $args[$message->getQueueId()][] = array(
                'Id' => $id++,
                'ReceiptHandle' => $message->getParameter('ReceiptHandle'),
                'VisibilityTimeout' => 0,
            );
        }

        foreach ($args as $queueId => $data) {
            $this->sqsClient->changeMessageVisibilityBatch(array(
                'QueueUrl' => $queueId,
                'Entries' => $data,
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function doDeleteMessages(array $messages)
    {
        /* @var array|Message[] $messages */
        $id = 1;

        $args = array();
        foreach ($messages as $message) {
            $args[$message->getQueueId()][] = array(
                'Id' => $id++,
                'ReceiptHandle' => $message->getParameter('ReceiptHandle'),
            );
        }

        foreach ($args as $queueId => $data) {
            $this->sqsClient->deleteMessageBatch(array(
                'QueueUrl' => $queueId,
                'Entries' => $data,
            ));
        }
    }

}