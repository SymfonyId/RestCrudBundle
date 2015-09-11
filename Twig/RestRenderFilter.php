<?php

namespace Symfonian\Indonesia\RestCrudBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Twig_Extension;
use Twig_SimpleFunction;

class RestRenderFilter extends Twig_Extension
{
    protected $serializer;

    public function __construct(ContainerInterface $container)
    {
        $this->serializer = $container->get('serializer');
    }

    public function getFunctions()
    {
        return array(
            new Twig_SimpleFunction('rest_render', array($this, 'render')),
        );
    }

    public function render($data, Request $request)
    {
        $format = $request->getRequestFormat();

        switch ($format)
        {
            case 'xml':
                return $this->renderXml($data);
                break;
            case 'json':
            default:
                return $this->renderJson($data);
                break;
        }
    }

    private function renderJson($data)
    {
        return $this->serializer->serialize($data, 'json');
    }

    private function renderXml($data)
    {
        return $this->serializer->serialize($data, 'xml');
    }

    public function getName()
    {
        return 'rest_render';
    }
}