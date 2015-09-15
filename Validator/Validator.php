<?php

namespace Symfonian\Indonesia\RestCrudBundle\Validator;

use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class Validator
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var Collection
     */
    private $constraints;

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
