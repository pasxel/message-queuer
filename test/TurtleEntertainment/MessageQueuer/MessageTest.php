<?php
/**
 * This file is part of the te/message-queuer package.
 *
 * (c) 2014 Turtle Entertainment GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use TurtleEntertainment\MessageQueuer\Message;

class MessageTest extends\PHPUnit_Framework_TestCase
{

    /**
     * @return Message
     */
    protected function getTestObject()
    {
        return new Message();
    }

    /**
     * @group Message
     */
    public function testIdProperty()
    {
        $object = $this->getTestObject();
        $this->assertNull($object->getId());

        $object->setId('123');
        $this->assertSame('123', $object->getId());
    }

    /**
     * @group Message
     */
    public function testQueueIdProperty()
    {
        $object = $this->getTestObject();
        $this->assertNull($object->getQueueId());

        $object->setQueueId('123');
        $this->assertSame('123', $object->getQueueId());
    }

    /**
     * @group Message
     */
    public function testDataProperty()
    {
        $object = $this->getTestObject();
        $this->assertNull($object->getData());

        $object->setData('123');
        $this->assertSame('123', $object->getData());
    }

    /**
     * @group Message
     */
    public function testParameterProperty()
    {
        $object = $this->getTestObject();
        $this->assertNull($object->getParameter('test1'));

        $object->setParameter('test1', '123');
        $this->assertSame('123', $object->getParameter('test1'));
    }

}