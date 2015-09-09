<?php

namespace Symfonian\Indonesia\RestCrudBundle\Event;

use Symfonian\Indonesia\RestCrudBundle\Form\FormInterface;
use Symfony\Component\EventDispatcher\Event;

class FilterFormEvent extends Event
{
    protected $form;

    protected $formData;

    public function setForm(FormInterface $form)
    {
        $this->form = $form;
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    public function setData($data)
    {
        $this->formData = $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->formData;
    }
}