<?php

namespace Symfonian\Indonesia\BundlePlugins\Tests;

class BootSpy
{
    private $wasCalled = false;

    public function wasCalled()
    {
        return $this->wasCalled;
    }

    public function call()
    {
        $this->wasCalled = true;
    }
}
