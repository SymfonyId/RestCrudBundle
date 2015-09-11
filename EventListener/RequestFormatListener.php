<?php

namespace Symfonian\Indonesia\RestCrudBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class RequestFormatListener
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        if ('html' === $request->getRequestFormat()) {
            $request->setRequestFormat('json');
        }
    }
}