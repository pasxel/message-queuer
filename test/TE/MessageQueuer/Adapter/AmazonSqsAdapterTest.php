<?php
/**
 * This file is part of the te/message-queuer package.
 *
 * (c) 2014 Turtle Entertainment GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use TE\MessageQueuer\Adapter\AmazonSqsAdapter;
use TE\MessageQueuer\Message;
use Guzzle\Service\Resource\Model;

class AmazonSqsAdapterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Aws\Sqs\SqsClient|PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockSqsClient;

    protected function setUp()
    {
        $this->mockSqsClient = $this->getMockBuilder('\Aws\Sqs\SqsClient')
                                    ->disableOriginalConstructor()
                                    ->setMethods(array('receiveMessage', 'sendMessageBatch', 'changeMessageVisibilityBatch', 'deleteMessageBatch'))
                                    ->getMock();
    }

    /**
     * @return AmazonSqsAdapter
     */
    protected function getTestObject()
    {
        return new AmazonSqsAdapter($this->mockSqsClient);
    }

    /**
     * @group AmazonSqsAdapter
     * @group sendMessage
     * @group flushMessage
     */
    public function testSendAndFlushMessages()
    {
        $object = $this->getTestObject();

        $message1 = new Message();
        $message1->setQueueId('http://sqs.us-east-1.amazonaws.com/123456789012/queue1');
        $message1->setData('testing data1...');
        $object->sendMessage($message1);

        $message2 = new Message();
        $message2->setQueueId('http://sqs.us-east-1.amazonaws.com/123456789012/queue1');
        $message2->setData('testing data2...');
        $object->sendMessage($message2);

        $message3 = new Message();
        $message3->setQueueId('http://sqs.us-east-1.amazonaws.com/123456789012/queue2');
        $message3->setData('testing data3...');
        $object->sendMessage($message3);

        // Mock for 1. sendMessageBatch call on SqsClient
        $this->mockSqsClient->expects($this->at(0))
                            ->method('sendMessageBatch')
                            ->with(array(
                                'QueueUrl' => 'http://sqs.us-east-1.amazonaws.com/123456789012/queue1',
                                'Entries' => array(
                                    array(
                                        'Id' => 1,
                                        'MessageBody' => 'testing data1...'
                                    ),
                                    array(
                                        'Id' => 2,
                                        'MessageBody' => 'testing data2...'
                                    )
                                )
                            ));

        // Mock for 2. sendMessageBatch call on SqsClient
        $this->mockSqsClient->expects($this->at(1))
                            ->method('sendMessageBatch')
                            ->with(array(
                                'QueueUrl' => 'http://sqs.us-east-1.amazonaws.com/123456789012/queue2',
                                'Entries' => array(
                                    array(
                                        'Id' => 3,
                                        'MessageBody' => 'testing data3...'
                                    )
                                )
                            ));

        // Run
        $object->flushMessages();
    }

    /**
     * @group AmazonSqsAdapter
     * @group consumeMessages
     */
    public function testConsumeMessages()
    {
        $data = array(
            'Messages' => array(
                array(
                    'MessageId'     => '11111111-1111-1111-1111-1111111111',
                    'Body'          => 'testing data1...',
                    'ReceiptHandle' => 'receipthandle_11111'
                ),
                array(
                    'MessageId'     => '22222222-2222-2222-2222-2222222222',
                    'Body'          => 'testing data2...',
                    'ReceiptHandle' => 'receipthandle_22222'
                )
            )
        );


        // Mock for receiveMessage call on SqsClient
        $this->mockSqsClient->expects($this->at(0))
                            ->method('receiveMessage')
                            ->with(array(
                                'QueueUrl' => 'http://sqs.us-east-1.amazonaws.com/123456789012/queue1',
                                'MaxNumberOfMessages' => 10
                            ))
                            ->will($this->returnValue(new Model($data)));

        // Consumer mock
        $consumer = $this->getMockBuilder('\TE\MessageQueuer\ConsumerInterface')
                         ->getMock();
        $consumer->expects($this->exactly(2))
                 ->method('handleMessage')
                 ->will($this->onConsecutiveCalls(array(false, true)));

        // Mock for deleteMessageBatch call on SqsClient
        $this->mockSqsClient->expects($this->at(1))
                            ->method('deleteMessageBatch')
                            ->with(array(
                                'QueueUrl' => 'http://sqs.us-east-1.amazonaws.com/123456789012/queue1',
                                'Entries' => array(
                                    array(
                                        'Id' => 1,
                                        'ReceiptHandle' => 'receipthandle_11111'
                                    )
                                )
                            ));

        // Mock for changeMessageVisibilityBatch call on SqsClient
        $this->mockSqsClient->expects($this->at(2))
                            ->method('changeMessageVisibilityBatch')
                            ->with(array(
                                'QueueUrl' => 'http://sqs.us-east-1.amazonaws.com/123456789012/queue1',
                                'Entries' => array(
                                    array(
                                        'Id' => 1,
                                        'ReceiptHandle' => 'receipthandle_22222',
                                        'VisibilityTimeout' => 0,
                                    )
                                )
                            ));

        // Run
        $this->getTestObject()->consumeMessages('http://sqs.us-east-1.amazonaws.com/123456789012/queue1', $consumer);
    }

    /**
     * @group AmazonSqsAdapter
     * @group consumeMessages
     */
    public function testConsumeMessagesNoMessagesInQueue()
    {
        $data = array();

        // Mock for receiveMessage call on SqsClient
        $this->mockSqsClient->expects($this->once())
                            ->method('receiveMessage')
                            ->with(array(
                                'QueueUrl' => 'http://sqs.us-east-1.amazonaws.com/123456789012/queue1',
                                'MaxNumberOfMessages' => 10
                            ))
                            ->will($this->returnValue(new Model($data)));

        // Consumer mock
        $consumer = $this->getMockBuilder('\TE\MessageQueuer\ConsumerInterface')
                          ->getMock();
        $consumer->expects($this->never())
                 ->method('handleMessage');

        // Run
        $this->getTestObject()->consumeMessages('http://sqs.us-east-1.amazonaws.com/123456789012/queue1', $consumer);
    }
}