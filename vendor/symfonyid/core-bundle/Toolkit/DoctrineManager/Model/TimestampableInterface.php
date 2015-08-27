<?php

namespace Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model;

use DateTime;

interface TimestampableInterface
{
    public function setCreatedAt(DateTime $date);

    public function getCreatedAt();

    public function setUpdatedAt(DateTime $date);

    public function getUpdatedAt();

    public function setCreatedBy($username);

    public function getCreatedBy();

    public function setUpdatedBy($username);

    public function getUpdatedBy();
}
