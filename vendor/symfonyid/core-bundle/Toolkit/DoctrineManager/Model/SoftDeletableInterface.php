<?php

namespace Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model;

use DateTime;

interface SoftDeletableInterface
{
    public function isDelete($isDelete = null);

    public function setDeletedAt(DateTime $date);

    public function getDeletedAt();

    public function setDeletedBy($username);

    public function getDeletedBy();
}
