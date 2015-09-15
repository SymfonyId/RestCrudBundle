<?php

namespace Symfonian\Indonesia\RestCrudBundle\Validator;

use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validator
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    protected $constraints;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function setConstraints(Collection $constraints)
    {
        $this->constraints = $constraints;
    }

    public function isValid($data)
    {
        return $this->validator->validate($data, $this->constraints);
    }
}