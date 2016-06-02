<?php

namespace Adadgio\GearBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcher;

class LoopController extends Controller
{
    public function workerAction(Request $request)
    {
        $dispatcher = $this->get('dispatcher');

        // create an event
        $event = new PayloadReceivedEvent();
        $event->setRequest($request);
        $event->setPayload(null);

        // and dispatch it
        $dispatcher->dispatch('', $event);

        return new JsonResponse(array('message' => 'nodered payload aknowledged '));
    }
}
