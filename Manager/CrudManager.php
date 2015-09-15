<?php

namespace Symfonian\Indonesia\RestCrudBundle\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Manager;
use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\ManagerFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

abstract class CrudManager extends Manager
{
    private $serializer;

    private $format;

    public function __construct(ManagerFactory $managerFactory, Request $request, TokenStorageInterface $tokenStorage, ObjectManager $objectManager, Serializer $serializer, $class, $format = 'json')
    {
        parent::__construct($managerFactory, $request, $tokenStorage, $objectManager, $class);
        $this->serializer = $serializer;
        $this->format = $format;
    }

    public function serialize($object)
    {
        $this->format = $this->request->getRequestFormat();
        $context = new SerializationContext();
        $context->setSerializeNull(true);

        return $this->serializer->serialize($object, $this->format, $context);
    }

    public function unserialize(array $data)
    {
        $this->format = $this->request->get('_format', $this->format);

        $this->serializer->deserialize($data, $this->class, $this->format);
    }

    protected function isSupportedObject($object)
    {
        return true;
    }
}
