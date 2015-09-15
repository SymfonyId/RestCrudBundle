<?php

namespace Symfonian\Indonesia\RestCrudBundle\Form;

use Symfonian\Indonesia\RestCrudBundle\Validator\Validator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;

abstract class Form implements FormInterface
{
    protected $fields = array();

    protected $data;

    protected $errors;

    protected $constraints;

    /**
     * @var Validator
     */
    protected $validator;

    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
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

    public function addConstraint($field, Constraint $constraint)
    {
        if (in_array($field, $this->fields)) {
            $this->constraints[$field][] = $constraint;
        }

        throw new \InvalidArgumentException(sprintf('Field %s is not found.', $field));
    }

    protected function addField($name, $type = 'string', $description = '', $constraint = null)
    {
        $this->fields[$name] = array($name, $type, $description);

        if (null !== $this->defaultConstraints()) {
            $this->addConstraint($name, $this->defaultConstraints());
        }

        if ($constraint) {
            if ($constraint instanceof Constraint) {
                $this->addConstraint($name, $constraint);
            }

            if (is_array($constraint)) {
                foreach ($constraint as $validation) {
                    $this->addConstraint($name, $validation);
                }
            }
        }
    }

    protected function addFields(array $fields)
    {
        foreach ($fields as $field) {
            $this->addField($field['name'], isset($field['type']) ?: 'string', isset($field['description']) ?: '');
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
        $this->validator->setConstraints(new Collection($this->constraints));
        $this->errors = $this->validator->isValid($this->data);
        if (count($this->errors) !== 0) {
            return false;
        }

        return true;
    }

    /**
     * @return Constraint | null
     */
    public function defaultConstraints()
    {
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getData()
    {
        return $this->data;
    }
}
