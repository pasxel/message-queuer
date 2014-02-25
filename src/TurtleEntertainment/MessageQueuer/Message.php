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
 * Message model
 *
 * @author Christian Eikermann <cei@turtle-entertainment.com>
 */
class Message
{
    /**
     * Unique identifier
     *
     * @var string
     */
    protected $id;

    /**
     * Unique identifier of the queue
     *
     * @var
     */
    protected $queueId;

    /**
     * Message body
     *
     * @var string
     */
    protected $data;

    /**
     * Adapter specific parameters
     *
     * @var array
     */
    protected $parameters = array();

    /**
     * Sets the queue id
     *
     * @param mixed $queueId
     *
     * @return self
     */
    public function setQueueId($queueId)
    {
        $this->queueId = $queueId;
        return $this;
    }

    /**
     * Returns the queue id
     *
     * @return string
     */
    public function getQueueId()
    {
        return $this->queueId;
    }

    /**
     * Sets the message body
     *
     * @param string $data
     *
     * @return self
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Sets the message id
     *
     * @param string $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns the message id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets an adapter-specific parameter
     *
     * @param string $key
     * @param mixed $value
     *
     * @return self
     */
    public function setParameter($key, $value)
    {
        $this->parameters[$key] = $value;
        return $this;
    }

    /**
     * Returns an adapter-specific parameter
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function getParameter($key, $default = null)
    {
        return isset($this->parameters[$key]) ? $this->parameters[$key] : $default;
    }

}