<?php

namespace Symfonian\Indonesia\RestCrudBundle\Annotation\Schema;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class Crud
{
    private $form;

    private $manager;

    public function __construct(array $data)
    {
        if (isset($data['value'])) {
            $this->manager = $data['value'];
        }

        if (isset($data['form'])) {
            $this->form = $data['form'];
        }

        if (isset($data['manager'])) {
            $this->manager = $data['manager'];
        }
    }

    public function getForm()
    {
        return $this->form;
    }

    public function getManager()
    {
        return $this->manager;
    }
}
