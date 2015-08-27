<?php

namespace Symfonian\Indonesia\RestCrudBundle\Annotation\Schema;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class Crud
{
    private $template;

    private $manager;

    public function __construct(array $data)
    {
        if ($data['template']) {
            $this->template = $data['template'];
        }

        if ($data['manager']) {
            $this->manager = $data['manager'];
        }
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function getManager()
    {
        return $this->manager;
    }
}