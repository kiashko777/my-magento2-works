<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Scans source code for references to classes and see if they indeed exist
 */

namespace Magento\Test\Legacy;

use Magento\Framework\App\Utility\AggregateInvoker;
use Magento\Framework\App\Utility\Classes;
use Magento\Framework\App\Utility\Files;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;

class ClassesTest extends TestCase
{
    public function testPhpCode()
    {
        $invoker = new AggregateInvoker($this);
        $invoker(
        /**
         * @param string $file
         */
            function ($file) {
                $classes = Classes::collectPhpCodeClasses(file_get_contents($file));
                $this->_assertNonFactoryName($classes, $file);
            },
            Files::init()->getPhpFiles(
                Files::INCLUDE_APP_CODE
                | Files::INCLUDE_PUB_CODE
                | Files::INCLUDE_LIBS
                | Files::INCLUDE_TEMPLATES
                | Files::AS_DATA_SET
                | Files::INCLUDE_NON_CLASSES
            )
        );
    }

    /**
     * Check whether specified classes or module names correspond to a file according PSR-1 Standard.
     *
     * Suppressing "unused variable" because of the "catch" block
     *
     * @param array $names
     * @param bool $softComparison
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _assertNonFactoryName($names, $file, $softComparison = false, $moduleBlock = false)
    {
        if (!$names) {
            return;
        }
        $factoryNames = [];
        foreach ($names as $name) {
            try {
                if ($softComparison) {
                    $this->assertDoesNotMatchRegularExpression('/\//', $name);
                } elseif ($moduleBlock) {
                    $this->assertFalse(false === strpos($name, '_'));
                    $this->assertMatchesRegularExpression('/^([A-Z][A-Za-z\d_]+)+$/', $name);
                } else {
                    if (strpos($name, 'Magento') === false) {
                        continue;
                    }
                    $this->assertFalse(false === strpos($name, '\\'));
                    $this->assertMatchesRegularExpression('/^([A-Z\\\\][A-Za-z\d\\\\]+)+$/', $name);
                }
            } catch (AssertionFailedError $e) {
                $factoryNames[] = $name;
            }
        }
        if ($factoryNames) {
            $this->fail("Obsolete factory name(s) detected in {$file}:" . "\n" . implode("\n", $factoryNames));
        }
    }

    public function testConfiguration()
    {
        $invoker = new AggregateInvoker($this);
        $invoker(
        /**
         * @param string $path
         */
            function ($path) {
                $xml = simplexml_load_file($path);

                $classes = Classes::collectClassesInConfig($xml);
                $this->_assertNonFactoryName($classes, $path);

                $modules = Classes::getXmlAttributeValues($xml, '//@module', 'module');
                $this->_assertNonFactoryName(array_unique($modules), $path, false, true);
            },
            Files::init()->getConfigFiles()
        );
    }

    public function testLayouts()
    {
        $invoker = new AggregateInvoker($this);
        $invoker(
        /**
         * @param string $path
         */
            function ($path) {
                $xml = simplexml_load_file($path);
                $classes = Classes::collectLayoutClasses($xml);
                foreach (Classes::getXmlAttributeValues(
                    $xml,
                    '/layout//@helper',
                    'helper'
                ) as $class) {
                    $classes[] = Classes::getCallbackClass($class);
                }
                $classes = array_merge(
                    $classes,
                    Classes::getXmlAttributeValues($xml, '/layout//@module', 'module')
                );
                $this->_assertNonFactoryName(array_unique($classes), $path);

                $tabs = Classes::getXmlNodeValues(
                    $xml,
                    '/layout//action[@method="addTab"]/block'
                );
                $this->_assertNonFactoryName(array_unique($tabs), $path, true);
            },
            Files::init()->getLayoutFiles()
        );
    }
}
