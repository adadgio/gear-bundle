<?php

namespace Adadgio\GearBundle\NodeRed\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\Event;
use Adadgio\GearBundle\NodeRed\PayloadInterface;

/**
 * An event that is dispatched when node red sends
 * back a payload to our application controller(s).
 */
class PayloadEvent extends Event
{
    /**
     * Event names for the dispatcher.
     */
    const PAYLOAD_RECEIVED = 'adadgio_gear.nodered.payload_received';
    const PAYLOAD_DISPATCHED = 'adadgio_gear.nodered.payload_dispatched';

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @var \Adadgio\GearBundle\Connector\NodeRed\PayloadInterface
     */
    protected $payload;

    /**
     * Get request.
     *
     * @return object \Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set request.
     *
     * @param  object \Request
     * @return object \Request
     */
    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Get payload.
     *
     * @return object \PayloadInterface
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * Set payload.
     *
     * @param  object \PayloadInterface
     * @return object \PayloadInterfaceReceivedEvent
     */
    public function setPayload(PayloadInterface $payload)
    {
        $this->payload = $payload;

        return $this;
    }
}
