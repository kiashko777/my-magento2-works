<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestFramework\Annotation;

use FilesystemIterator;
use InvalidArgumentException;
use Magento\Framework\Component\ComponentRegistrar;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use RegexIterator;
use SplFileInfo;

/**
 * Implementation of the @magentoComponentsDir DocBlock annotation
 */
class ComponentRegistrarFixture
{
    /**
     * Annotation name
     */
    const ANNOTATION_NAME = 'magentoComponentsDir';

    /**#@+
     * Properties of components registrar
     */
    const REGISTRAR_CLASS = ComponentRegistrar::class;
    const PATHS_FIELD = 'paths';
    /**#@-*/

    /**
     * Fixtures base dir
     *
     * @var string
     */
    private $fixtureBaseDir;

    /**
     * Original values of registered components
     *
     * @var array
     */
    private $origComponents = null;

    /**
     * Constructor
     *
     * @param string $fixtureBaseDir
     */
    public function __construct($fixtureBaseDir)
    {
        $this->fixtureBaseDir = $fixtureBaseDir;
    }

    /**
     * Handler for 'startTest' event
     *
     * @param TestCase $test
     * @return void
     */
    public function startTest(TestCase $test)
    {
        $this->registerComponents($test);
    }

    /**
     * Register fixture components
     *
     * @param TestCase $test
     */
    private function registerComponents(TestCase $test)
    {
        $annotations = $test->getAnnotations();
        $componentAnnotations = [];
        if (isset($annotations['class'][self::ANNOTATION_NAME])) {
            $componentAnnotations = array_merge($componentAnnotations, $annotations['class'][self::ANNOTATION_NAME]);
        }
        if (isset($annotations['method'][self::ANNOTATION_NAME])) {
            $componentAnnotations = array_merge($componentAnnotations, $annotations['method'][self::ANNOTATION_NAME]);
        }
        if (empty($componentAnnotations)) {
            return;
        }
        $componentAnnotations = array_unique($componentAnnotations);
        $reflection = new ReflectionClass(self::REGISTRAR_CLASS);
        $paths = $reflection->getProperty(self::PATHS_FIELD);
        $paths->setAccessible(true);
        $this->origComponents = $paths->getValue();
        $paths->setAccessible(false);
        foreach ($componentAnnotations as $fixturePath) {
            $fixturesDir = $this->fixtureBaseDir . '/' . $fixturePath;
            if (!file_exists($fixturesDir)) {
                throw new InvalidArgumentException(
                    self::ANNOTATION_NAME . " fixture '$fixturePath' does not exist"
                );
            }
            $iterator = new RegexIterator(
                new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($fixturesDir, FilesystemIterator::SKIP_DOTS)
                ),
                '/^.+\/registration\.php$/'
            );
            /**
             * @var SplFileInfo $registrationFile
             */
            foreach ($iterator as $registrationFile) {
                require $registrationFile->getRealPath();
            }
        }
    }

    /**
     * Handler for 'endTest' event
     *
     * @param TestCase $test
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function endTest(TestCase $test)
    {
        $this->restoreComponents();
    }

    /**
     * Restore registered components list to the original
     */
    private function restoreComponents()
    {
        if (null !== $this->origComponents) {
            $reflection = new ReflectionClass(self::REGISTRAR_CLASS);
            $paths = $reflection->getProperty(self::PATHS_FIELD);
            $paths->setAccessible(true);
            $paths->setValue($this->origComponents);
            $paths->setAccessible(false);
            $this->origComponents = null;
        }
    }
}
