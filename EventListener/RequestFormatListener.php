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
        if ('xml' === $request->getRequestFormat()) {
            $request->setRequestFormat('json');
        }
    }
}