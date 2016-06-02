<?php

namespace Adadgio\GearBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcher;

use Adadgio\GearBundle\Connector\NodeRed\NodeRedConnector;
use Adadgio\GearBundle\Connector\NodeRed\PayloadInterface;
use Adadgio\GearBundle\Connector\NodeRed\Event\PayloadEvent;

class LoopController extends Controller
{
    public function receivePayloadAction(Request $request)
    {
        // first create a payload object from the POST data
        $payload = NodeRedConnector::createPayloadFromRequest($request);

        // create an event
        $event = new PayloadEvent();
        $event->setRequest($request);
        $event->setPayload($payload);

        // and dispatch it
        $this->get('event_dispatcher')->dispatch(PayloadEvent::PAYLOAD_RECEIVED, $event);

        // $payload->kill();
        // $payload->live();
        if ($payload->getIteration() > 16) {
            $payload->kill();
        }
        
        // now continue the loop (send another payload)
        // the event passed to the user might have modified the payload
        $payload = $event->getPayload();

        return new JsonResponse($payload->getParameters());
    }

    private function dispatchPayloadExampleAction()
    {
        $payload = new \Adadgio\GearBundle\Connector\NodeRed\Payload();

        $response = $this
            ->get('adadgio_gear.nodered.connector')
            ->send('POST', '/adadgio/gear/loop/start', $payload);

        return $response;
    }
}
