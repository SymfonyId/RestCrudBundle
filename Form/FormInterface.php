<?php

namespace Symfonian\Indonesia\RestCrudBundle\Form;

use Symfony\Component\HttpFoundation\Request;

interface FormInterface
{
    public function toArray();

    public function configure();

    public function isValid();

    public function getErrors();

    public function getData();

    public function getName();

    public function defaultConstraints();

    public function handleRequest(Request $request);
}
