<?php
/**
 * This file is part of the te/message-queuer package.
 *
 * (c) 2014 Turtle Entertainment GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MessageQueuer;

/**
 * Interface for any message queue adapter
 *
 * @author Christian Eikermann <cei@turtle-entertainment.com>
 */
interface MQAdapterInterface
{

    /**
     * Send message to queue
     *
     * @param Message $message
     */
    public function sendMessage(Message $message);

    /**
     * Flush all pending messages to queue
     */
    public function flushMessages();

    /**
     * Consume messages from queue
     *
     * @param string            $queueId
     * @param ConsumerInterface $consumer
     */
    public function consumeMessages($queueId, ConsumerInterface $consumer);

} 