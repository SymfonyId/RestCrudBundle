<?php

namespace Symfonian\Indonesia\RestCrudBundle\Annotation\Reader;

use Doctrine\Common\Annotations\Reader;
use ReflectionObject;
use Symfonian\Indonesia\RestCrudBundle\Annotation\Schema\Crud;
use Symfonian\Indonesia\RestCrudBundle\Controller\CrudController;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class AnnotationReader
{
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        if (!is_array($controller)) {
            return;
        }

        $controller = $controller[0];
        if (!$controller instanceof CrudController) {
            return;
        }

        $reflectionObject = new ReflectionObject($controller);
        foreach ($this->reader->getClassAnnotations($reflectionObject) as $annotation) {
            if ($annotation instanceof Crud) {
                $controller->setManager($annotation->getManager());
                $controller->setTemplate($annotation->getTemplate());
            }
        }
    }
}
