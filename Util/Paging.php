<?php

namespace Symfonian\Indonesia\RestCrudBundle\Util;

class Paging
{
    private $current;

    private $next;

    private $previous;

    private $records;

    public function setCurrent($current)
    {
        $this->current = $current;
    }

    public function setNext($next)
    {
        $this->next = $next;
    }

    public function setPrevious($previous)
    {
        $this->previous = $previous;
    }

    public function setRecords($records)
    {
        $this->records = $records;
    }
}
