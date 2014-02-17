<?php
/**
 * This file is part of the te/message-queuer package.
 *
 * (c) 2014 Turtle Entertainment GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MessageQueuer\Adapter;

use MessageQueuer\ConsumerInterface;
use MessageQueuer\Message;
use MessageQueuer\MQAdapterInterface;

/**
 * Abstract adapter class
 *
 * @author Christian Eikermann <cei@turtle-entertainment.com>
 */
abstract class AbstractAdapter implements MQAdapterInterface
{
    /**
     * Pending messages
     *
     * @var array|Message[]
     */
    protected $messages;

    /**
     * {@inheritdoc}
     */
    public function sendMessage(Message $message)
    {
        $key = spl_object_hash($message);
        $this->messages[$key] = $message;
    }

    /**
     * {@inheritdoc}
     */
    public function consumeMessages($queueId, ConsumerInterface $consumer)
    {
        $releaseMessages = array();
        $deleteMessages = array();

        $messages = $this->doReceiveMessages($queueId);
        foreach ($messages as $message) {
            if ($consumer->handleMessage($message)) {
                $deleteMessages[] = $message;
            } else {
                $releaseMessages[] = $message;
            }
        }

        $this->doDeleteMessages($deleteMessages);
        $this->doReleaseMessages($releaseMessages);
    }

    /**
     * {@inheritdoc}
     */
    public function flushMessages()
    {
        $this->doSendMessages($this->messages);
    }

    /**
     * Internal method to receive messages from queue
     *
     * @param $queueId
     *
     * @return array|Message[]
     */
    abstract protected function doReceiveMessages($queueId);

    /**
     * Internal method to send messages to queue
     *
     * @param array|Message[] $messages
     *
     * @return void
     */
    abstract protected function doSendMessages(array $messages);

    /**
     * Internal method to release messages on queue
     *
     * @param array|Message[] $messages
     *
     * @return void
     */
    abstract protected function doReleaseMessages(array $messages);

    /**
     * Internal method to delete messages from queue
     *
     * @param array|Message[] $messages
     *
     * @return void
     */
    abstract protected function doDeleteMessages(array $messages);

}