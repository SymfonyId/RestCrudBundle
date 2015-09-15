<?php

namespace Symfonian\Indonesia\RestCrudBundle\Event;

use Symfony\Component\HttpFoundation\Response;

class FilterValidationEvent extends FilterFormEvent
{
    private $response;

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
}
