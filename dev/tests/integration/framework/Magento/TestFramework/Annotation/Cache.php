<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestFramework\Annotation;

use Exception;
use Magento\Framework\App\Cache\StateInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Implementation of the @magentoCache DocBlock annotation
 */
class Cache
{
    /**
     * Original values for cache type states
     *
     * @var array
     */
    private $origValues = [];

    /**
     * Handler for 'startTest' event
     *
     * @param TestCase $test
     * @return void
     */
    public function startTest(TestCase $test)
    {
        $source = $test->getAnnotations();
        if (isset($source['method']['magentoCache'])) {
            $annotations = $source['method']['magentoCache'];
        } elseif (isset($source['class']['magentoCache'])) {
            $annotations = $source['class']['magentoCache'];
        } else {
            return;
        }
        $this->setValues($this->parseValues($annotations, $test), $test);
    }

    /**
     * Sets the values of cache types
     *
     * @param array $values
     * @param TestCase $test
     */
    private function setValues($values, TestCase $test)
    {
        $typeList = self::getTypeList();
        if (!$this->origValues) {
            $this->origValues = [];
            foreach ($typeList->getTypes() as $type => $row) {
                $this->origValues[$type] = $row['status'];
            }
        }
        /** @var StateInterface $states */
        $states = Bootstrap::getInstance()->getObjectManager()->get(StateInterface::class);
        foreach ($values as $type => $isEnabled) {
            if (!isset($this->origValues[$type])) {
                self::fail("Unknown cache type specified: '{$type}' in @magentoCache", $test);
            }
            $states->setEnabled($type, $isEnabled);
        }
    }

    /**
     * Getter for cache types list
     *
     * @return TypeListInterface
     */
    private static function getTypeList()
    {
        return Bootstrap::getInstance()->getObjectManager()->get(TypeListInterface::class);
    }

    /**
     * Fails the test with specified error message
     *
     * @param string $message
     * @param TestCase $test
     * @throws Exception
     */
    private static function fail($message, TestCase $test)
    {
        $test->fail("{$message} in the test '{$test->toString()}'");
        throw new Exception('The above line was supposed to throw an exception.');
    }

    /**
     * Determines from docblock annotations which cache types to set
     *
     * @param array $annotations
     * @param TestCase $test
     * @return array
     */
    private function parseValues($annotations, TestCase $test)
    {
        $result = [];
        $typeList = self::getTypeList();
        foreach ($annotations as $subject) {
            if (!preg_match('/^([a-z_]+)\s(enabled|disabled)$/', $subject, $matches)) {
                self::fail("Invalid @magentoCache declaration: '{$subject}'", $test);
            }
            list(, $requestedType, $isEnabled) = $matches;
            $isEnabled = $isEnabled == 'enabled' ? 1 : 0;
            if ('all' === $requestedType) {
                $result = [];
                foreach ($typeList->getTypes() as $type) {
                    $result[$type['id']] = $isEnabled;
                }
            } else {
                $result[$requestedType] = $isEnabled;
            }
        }
        return $result;
    }

    /**
     * Handler for 'endTest' event
     *
     * @param TestCase $test
     * @return void
     */
    public function endTest(TestCase $test)
    {
        if ($this->origValues) {
            $this->setValues($this->origValues, $test);
            $this->origValues = [];
        }
    }
}
