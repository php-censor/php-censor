<?php

namespace Tests\PHPCensor;

class LocalizationTestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Returns a string representation of the test case.
     *
     * @return string
     */
    public function toString()
    {
        $class = new \ReflectionClass($this);

        $buffer = sprintf(
            '%s::%s',
            $class->name,
            $this->getName(false)
        );

        return $buffer . $this->getDataSetAsString(false);
    }
}
