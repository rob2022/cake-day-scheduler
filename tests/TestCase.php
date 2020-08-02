<?php
declare(strict_types=1);

namespace CakeDay;
use CakeDay\Entities\Person;

class TestCase extends \PHPUnit\Framework\TestCase
{
    public function getSimpleMock(string $class)
    {
        return $this->getMockBuilder($class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
    }
}
