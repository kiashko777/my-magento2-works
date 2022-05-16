<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Implementation of the @magentoSchemaFixture DocBlock annotation.
 */

namespace Magento\TestFramework\Annotation;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use PHPUnit\Framework\TestCase;

/**
 * Represents following construction handling:
 *
 * @magentoSchemaFixture {link_to_file.php}
 */
class SchemaFixture
{
    /**
     * Fixtures base directory.
     *
     * @var string
     */
    protected $fixtureBaseDir;

    /**
     * Fixtures that have been applied.
     *
     * @var array
     */
    private $appliedFixtures = [];

    /**
     * Constructor.
     *
     * @param string $fixtureBaseDir
     * @throws LocalizedException
     */
    public function __construct($fixtureBaseDir)
    {
        if (!is_dir($fixtureBaseDir)) {
            throw new LocalizedException(
                new Phrase("Fixture base directory '%1' does not exist.", [$fixtureBaseDir])
            );
        }
        $this->fixtureBaseDir = realpath($fixtureBaseDir);
    }

    /**
     * Apply magento data fixture on.
     *
     * @param TestCase $test
     * @return void
     */
    public function startTest(TestCase $test)
    {
        if ($this->_getFixtures($test)) {
            $this->_applyFixtures($this->_getFixtures($test));
        }
    }

    /**
     * Retrieve fixtures from annotation.
     *
     * @param TestCase $test
     * @param string $scope
     * @return array
     * @throws LocalizedException
     */
    protected function _getFixtures(TestCase $test, $scope = null)
    {
        if ($scope === null) {
            $annotations = $this->getAnnotations($test);
        } else {
            $annotations = $test->getAnnotations()[$scope];
        }
        $result = [];
        if (!empty($annotations['magentoSchemaFixture'])) {
            foreach ($annotations['magentoSchemaFixture'] as $fixture) {
                if (strpos($fixture, '\\') !== false) {
                    // usage of a single directory separator symbol streamlines search across the source code
                    throw new LocalizedException(
                        new Phrase('Directory separator "\\" is prohibited in fixture declaration.')
                    );
                }
                $fixtureMethod = [get_class($test), $fixture];
                if (is_callable($fixtureMethod)) {
                    $result[] = $fixtureMethod;
                } else {
                    $result[] = $this->fixtureBaseDir . '/' . $fixture;
                }
            }
        }
        return $result;
    }

    /**
     * Get annotations for test.
     *
     * @param TestCase $test
     * @return array
     */
    private function getAnnotations(TestCase $test)
    {
        $annotations = $test->getAnnotations();
        return array_replace($annotations['class'], $annotations['method']);
    }

    /**
     * Execute fixture scripts if any.
     *
     * @param array $fixtures
     * @throws LocalizedException
     */
    protected function _applyFixtures(array $fixtures)
    {
        /* Execute fixture scripts */
        foreach ($fixtures as $oneFixture) {
            /* Skip already applied fixtures */
            if (in_array($oneFixture, $this->appliedFixtures, true)) {
                continue;
            }
            $this->_applyOneFixture($oneFixture);
            $this->appliedFixtures[] = $oneFixture;
        }
    }

    /**
     * Execute single fixture script.
     *
     * @param string|array $fixture
     * @throws Exception
     */
    protected function _applyOneFixture($fixture)
    {
        try {
            if (is_callable($fixture)) {
                call_user_func($fixture);
            } else {
                include $fixture;
            }
        } catch (Exception $e) {
            throw new Exception(
                sprintf("Error in fixture: %s.\n %s", json_encode($fixture), $e->getMessage()),
                500,
                $e
            );
        }
    }

    /**
     * Finish test execution.
     *
     * @param TestCase $test
     */
    public function endTest(TestCase $test)
    {
        if ($this->_getFixtures($test)) {
            $this->_revertFixtures();
        }
    }

    /**
     * Revert changes done by fixtures.
     */
    protected function _revertFixtures()
    {
        foreach ($this->appliedFixtures as $fixture) {
            if (is_callable($fixture)) {
                $fixture[1] .= 'Rollback';
                if (is_callable($fixture)) {
                    $this->_applyOneFixture($fixture);
                }
            } else {
                $fileInfo = pathinfo($fixture);
                $extension = '';
                if (isset($fileInfo['extension'])) {
                    $extension = '.' . $fileInfo['extension'];
                }
                $rollbackScript = $fileInfo['dirname'] . '/' . $fileInfo['filename'] . '_rollback' . $extension;
                if (file_exists($rollbackScript)) {
                    $this->_applyOneFixture($rollbackScript);
                }
            }
        }
        $this->appliedFixtures = [];
    }
}
