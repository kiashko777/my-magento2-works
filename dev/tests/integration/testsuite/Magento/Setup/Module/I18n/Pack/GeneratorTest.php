<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\I18n\Pack;

use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\System\Dirs;
use Magento\Setup\Module\I18n\ServiceLocator;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class GeneratorTest extends TestCase
{
    /**
     * @var string
     */
    protected $_testDir;

    /**
     * @var string
     */
    protected $_expectedDir;

    /**
     * @var string
     */
    protected $_dictionaryPath;

    /**
     * @var string
     */
    protected $_packPath;

    /**
     * @var string
     */
    protected $_locale;

    /**
     * @var array
     */
    protected $_expectedFiles;

    /**
     * @var Generator
     */
    protected $_generator;

    /**
     * @var array
     */
    protected $backupRegistrar;

    public function testGeneration()
    {
        $this->assertFileDoesNotExist($this->_packPath);

        ComponentRegistrar::register(
            ComponentRegistrar::MODULE,
            'Magento_FirstModule',
            $this->_packPath . '/app/code/Magento/FirstModule'
        );
        ComponentRegistrar::register(
            ComponentRegistrar::MODULE,
            'Magento_SecondModule',
            $this->_packPath . '/app/code/Magento/SecondModule'
        );
        ComponentRegistrar::register(
            ComponentRegistrar::THEME,
            'Adminhtml/default',
            $this->_packPath . '/app/design/Adminhtml/default'
        );

        $this->_generator->generate($this->_dictionaryPath, $this->_locale);

        foreach ($this->_expectedFiles as $file) {
            $this->assertFileEquals($this->_expectedDir . $file, $this->_packPath . $file);
        }
    }

    protected function setUp(): void
    {
        $this->_testDir = realpath(__DIR__ . '/_files');
        $this->_expectedDir = $this->_testDir . '/expected';
        $this->_dictionaryPath = $this->_testDir . '/source.csv';
        $this->_packPath = $this->_testDir . '/pack';
        $this->_locale = 'de_DE';
        $this->_expectedFiles = [
            "/app/code/Magento/FirstModule/i18n/{$this->_locale}.csv",
            "/app/code/Magento/SecondModule/i18n/{$this->_locale}.csv",
            "/app/design/Adminhtml/default/i18n/{$this->_locale}.csv",
        ];

        $this->_generator = ServiceLocator::getPackGenerator();

        Dirs::rm($this->_packPath);

        $reflection = new ReflectionClass(ComponentRegistrar::class);
        $paths = $reflection->getProperty('paths');
        $paths->setAccessible(true);
        $this->backupRegistrar = $paths->getValue();
        $paths->setAccessible(false);
    }

    protected function tearDown(): void
    {
        Dirs::rm($this->_packPath);
        $reflection = new ReflectionClass(ComponentRegistrar::class);
        $paths = $reflection->getProperty('paths');
        $paths->setAccessible(true);
        $paths->setValue($this->backupRegistrar);
        $paths->setAccessible(false);
    }
}
