<?php

namespace Symfonian\Indonesia\RestCrudBundle\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\Event;

class FilterRequestEvent extends Event
{
    private $request;

    private $response;

    private $controller;

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * @return \Symfonian\Indonesia\RestCrudBundle\Controller\CrudController
     */
    public function getController()
    {
        return $this->controller;
    }
}