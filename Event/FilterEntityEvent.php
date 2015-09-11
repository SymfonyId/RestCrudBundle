<?php

namespace Symfonian\Indonesia\RestCrudBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Manager;
use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\EntityInterface;

class FilterEntityEvent extends Event
{
    protected $request;

    protected $response;

    protected $manager;

    protected $entity;

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

    public function setManager(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return Manager
     */
    public function getManager()
    {
        return $this->manager;
    }

    public function setEntity(EntityInterface $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return EntityInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }
}