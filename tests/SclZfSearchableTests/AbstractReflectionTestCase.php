<?php
/**
 * @todo Remove this, it's only kept here for reference.
 */

namespace SclObjectManager;

abstract class AbstractReflectionTestCase extends \PHPUnit_Framework_TestCase
{
    protected function mockedThisMethod($className, $methodName, $args = array(), $setUpMock = null)
    {
        $mock = $this->getMockBuilder($className)
            ->disableOriginalConstructor()
            ->getMock();

        if ($setUpMock !== null) {
            $setUpMock($mock, $this);
        }

        $reflectionClass = new \ReflectionClass($className);
        $method = $reflectionClass->getMethod($methodName);
        return $method->invokeArgs($mock, $args);
    }
}
