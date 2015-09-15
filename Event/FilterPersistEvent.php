<?php

namespace Symfonian\Indonesia\RestCrudBundle\Event;

use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Manager;
use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\EntityInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class FilterPersistEvent extends Event
{
    private $entity;

    private $manager;

    private $request;

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
}
