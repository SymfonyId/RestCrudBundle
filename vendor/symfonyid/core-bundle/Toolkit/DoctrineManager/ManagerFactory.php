<?php

namespace Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager;

class ManagerFactory
{
    protected $managers = array();

    public function addManager(Manager $manager)
    {
        $this->managers[$manager->getName()] = $manager;
    }

    public function getManager($manager)
    {
        if (array_key_exists($manager, $this->managers)) {
            return $this->managers[$manager];
        }

        throw new \InvalidArgumentException(sprintf('Manager with name %s is not found.'));
    }
}