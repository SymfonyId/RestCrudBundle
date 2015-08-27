<?php

namespace Symfonian\Indonesia\CoreBundle\Toolkit\MicroCache;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Response;

class MicroCacheListener
{
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if ($event->getRequest()->isMethod('GET')) {
            $response = $event->getResponse();

            $response->setPublic();
            $response->setMaxAge(3);
            $response->setSharedMaxAge(3);
            $response->setETag(md5($response->getContent()));
        }
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if ($request->isMethod('GET')) {
            $response = new Response();

            if ($response->isNotModified($request)) {
                $event->setResponse($response);
            }
        }
    }
}
