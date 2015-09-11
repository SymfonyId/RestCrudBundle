<?php

namespace Symfonian\Indonesia\RestCrudBundle\Form;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class Form implements FormInterface
{
    protected $fields = array();

    protected $data;

    protected $errors;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var Collection
     */
    protected $constraints;

    public function __construct()
    {
        $this->configure();
    }

    public function toArray()
    {
        $output = array();
        foreach ($this->fields as $field) {
            $output[] = array(
                'name' => $field[0],
                'type' => $field[1],
                'description' => $field[2],
            );
        }

        return array($this->getName() => $output);
    }

    protected function addField($name, $type = 'string', $description = '')
    {
        $this->fields[$name] = array($name, $type, $description);
    }

    protected function addFields(array $fields)
    {
        foreach ($fields as $field) {
            $this->addField($field['name'], isset($field['type'])?: 'string', isset($field['description'])?: '');
        }
    }

    public function handleRequest(Request $request)
    {
        $data = $request->get($this->getName());
        $fields = array_keys($this->fields);
        foreach ($data as $field => $value) {
            if (in_array($field, $fields)) {
                $this->data[$field] = $value;
            }
        }
    }

    public function isValid()
    {
        $this->errors = $this->validator->validate($this->data, $this->constraints);
        if (count($this->errors) !== 0) {
            return false;
        }

        return true;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function setValidationConstraints(Collection $collection)
    {
        $this->constraints = $collection;
    }

    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function getData()
    {
        return $this->data;
    }
}