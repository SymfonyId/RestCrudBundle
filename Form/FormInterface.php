<?php

namespace Symfonian\Indonesia\RestCrudBundle\Form;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validator\ValidatorInterface;

interface FormInterface
{
    public function toArray();

    public function addField($name, $type, $description = '');

    public function addFields(array $fields);

    public function setValidator(ValidatorInterface $validator);

    public function setValidationConstraints(Collection $collection);

    public function isValid();

    public function getErrors();

    public function getData();

    public function getName();

    public function handleRequest(Request $request);
}