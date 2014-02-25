<?php
/**
 * This file is part of the te/message-queuer package.
 *
 * (c) 2014 Turtle Entertainment GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TurtleEntertainment\MessageQueuer;

/**
 * Consumer interface
 *
 * @author Christian Eikermann <cei@turtle-entertainment.com>
 */
interface ConsumerInterface
{

    /**
     * Handle one message from the queue
     *
     * @param Message $message
     *
     * @return bool On success: message will be deleted - On failure: message will be pushed back into the queue
     */
    public function handleMessage(Message $message);

} 