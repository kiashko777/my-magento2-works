<?php
/**
 * Compiler test. Check compilation of DI definitions and code generation
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test\Integrity\Di;

use DOMDocument;
use DOMNode;
use DOMXPath;
use Exception;
use Magento\Framework\Api\Code\Generator\ExtensionAttributesGenerator;
use Magento\Framework\Api\Code\Generator\ExtensionAttributesInterfaceGenerator;
use Magento\Framework\Api\Code\Generator\Mapper;
use Magento\Framework\Api\Code\Generator\SearchResults;
use Magento\Framework\App\Arguments\ArgumentInterpreter;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Utility\AggregateInvoker;
use Magento\Framework\App\Utility\Classes;
use Magento\Framework\App\Utility\Files;
use Magento\Framework\Code\Generator;
use Magento\Framework\Code\Generator\Autoloader;
use Magento\Framework\Code\Generator\Io;
use Magento\Framework\Code\Validator;
use Magento\Framework\Code\Validator\ArgumentSequence;
use Magento\Framework\Code\Validator\ConstructorArgumentTypes;
use Magento\Framework\Code\Validator\ConstructorIntegrity;
use Magento\Framework\Code\Validator\TypeDuplication;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Data\Argument\Interpreter\ArrayType;
use Magento\Framework\Data\Argument\Interpreter\BaseStringUtils;
use Magento\Framework\Data\Argument\Interpreter\Boolean;
use Magento\Framework\Data\Argument\Interpreter\Composite;
use Magento\Framework\Data\Argument\Interpreter\Constant;
use Magento\Framework\Data\Argument\Interpreter\DataObject;
use Magento\Framework\Data\Argument\Interpreter\NullType;
use Magento\Framework\Data\Argument\Interpreter\Number;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Interception\Code\InterfaceValidator;
use Magento\Framework\ObjectManager\Code\Generator\Converter;
use Magento\Framework\ObjectManager\Code\Generator\Factory;
use Magento\Framework\ObjectManager\Code\Generator\Repository;
use Magento\Framework\ObjectManager\Config\Mapper\ArgumentParser;
use Magento\Framework\ObjectManager\Config\Mapper\Dom;
use Magento\Framework\Shell;
use Magento\Framework\Shell\CommandRenderer;
use Magento\Framework\Stdlib\BooleanUtils;
use Magento\TestFramework\Integrity\PluginValidator;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CompilerTest extends TestCase
{
    /**
     * @var string
     */
    protected $_command;

    /**
     * @var Shell
     */
    protected $_shell;

    /**
     * @var string
     */
    protected $_generationDir;

    /**
     * @var string
     */
    protected $_compilationDir;

    /**
     * @var Dom()
     */
    protected $_mapper;

    /**
     * @var Validator
     */
    protected $_validator;

    /**
     * Class arguments reader
     *
     * @var PluginValidator
     */
    protected $pluginValidator;

    /**
     * @var string[]|null
     */
    private $pluginBlacklist;

    /**
     * Validate DI configuration
     */
    public function testConfigurationOfInstanceParameters()
    {
        $invoker = new AggregateInvoker($this);
        $invoker(
            function ($file) {
                $this->_validateFile($file);
            },
            Files::init()->getDiConfigs(true)
        );
    }

    /**
     * Validate DI config file
     *
     * @param string $file
     */
    protected function _validateFile($file)
    {
        $dom = new DOMDocument();
        $dom->load($file);
        $data = $this->_mapper->convert($dom);

        foreach ($data as $instanceName => $parameters) {
            if (!isset($parameters['parameters']) || empty($parameters['parameters'])) {
                continue;
            }
            if (Classes::isVirtual($instanceName)) {
                $instanceName = Classes::resolveVirtualType($instanceName);
            }

            if (!$this->_classExistsAsReal($instanceName)) {
                continue;
            }

            $reflectionClass = new ReflectionClass($instanceName);

            $constructor = $reflectionClass->getConstructor();
            if (!$constructor) {
                $this->fail('Class ' . $instanceName . ' does not have __constructor');
            }

            $parameters = $parameters['parameters'];
            $classParameters = $constructor->getParameters();
            foreach ($classParameters as $classParameter) {
                $parameterName = $classParameter->getName();
                if (array_key_exists($parameterName, $parameters)) {
                    unset($parameters[$parameterName]);
                }
            }
            $message = 'Configuration of ' . $instanceName . ' contains data for non-existed parameters: ' . implode(
                    ', ',
                    array_keys($parameters)
                );
            $this->assertEmpty($parameters, $message);
        }
    }

    /**
     * Checks if class is a real one or generated Factory
     * @param string $instanceName class name
     * @return bool
     * @throws AssertionFailedError
     */
    protected function _classExistsAsReal($instanceName)
    {
        if (class_exists($instanceName)) {
            return true;
        }
        // check for generated factory
        if (substr($instanceName, -7) == 'Factory' && class_exists(substr($instanceName, 0, -7))) {
            return false;
        }
        $this->fail('Detected configuration of non existed class: ' . $instanceName);
    }

    /**
     * Validate constructor integrity
     */
    public function testConstructorIntegrity()
    {
        $generatorIo = new Io(
            new File(),
            $this->_generationDir
        );
        $generator = new Generator(
            $generatorIo,
            [
                Factory::ENTITY_TYPE => Factory::class,
                Repository::ENTITY_TYPE => Repository::class,
                Converter::ENTITY_TYPE => Converter::class,
                Mapper::ENTITY_TYPE => Mapper::class,
                SearchResults::ENTITY_TYPE => SearchResults::class,
                ExtensionAttributesInterfaceGenerator::ENTITY_TYPE =>
                    ExtensionAttributesInterfaceGenerator::class,
                ExtensionAttributesGenerator::ENTITY_TYPE =>
                    ExtensionAttributesGenerator::class
            ]
        );
        $generationAutoloader = new Autoloader($generator);
        spl_autoload_register([$generationAutoloader, 'load']);

        $invoker = new AggregateInvoker($this);
        $invoker(
            function ($className) {
                $this->_validateClass($className);
            },
            $this->_phpClassesDataProvider()
        );
        spl_autoload_unregister([$generationAutoloader, 'load']);
    }

    /**
     * Validate class
     *
     * @param string $className
     */
    protected function _validateClass($className)
    {
        try {
            $this->_validator->validate($className);
        } catch (ValidatorException $exceptions) {
            $this->fail($exceptions->getMessage());
        } catch (ReflectionException $exceptions) {
            $this->fail($exceptions->getMessage());
        }
    }

    /**
     * Get php classes list
     *
     * @return array
     */
    protected function _phpClassesDataProvider()
    {
        $generationPath = str_replace('/', '\\', $this->_generationDir);

        $files = Files::init()->getPhpFiles(Files::INCLUDE_APP_CODE | Files::INCLUDE_LIBS);

        $patterns = ['/' . preg_quote($generationPath) . '/',];
        $replacements = [''];

        $componentRegistrar = new ComponentRegistrar();
        foreach ($componentRegistrar->getPaths(ComponentRegistrar::MODULE) as $moduleName => $modulePath) {
            $patterns[] = '/' . preg_quote(str_replace('/', '\\', $modulePath)) . '/';
            $replacements[] = '\\' . str_replace('_', '\\', $moduleName);
        }

        foreach ($componentRegistrar->getPaths(ComponentRegistrar::LIBRARY) as $libPath) {
            $patterns[] = '/' . preg_quote(str_replace('/', '\\', $libPath)) . '/';
            $replacements[] = '\\Magento\\Framework';
        }

        /** Convert file names into class name format */
        $classes = [];
        foreach ($files as $file) {
            $file = str_replace('/', '\\', $file);
            $filePath = preg_replace($patterns, $replacements, $file);
            $className = substr($filePath, 0, -4);
            if (class_exists($className, false)) {
                $file = str_replace('\\', DIRECTORY_SEPARATOR, $file);
                $classes[$file] = $className;
            }
        }

        /** Build class inheritance hierarchy  */
        $output = [];
        $allowedFiles = array_keys($classes);
        foreach ($classes as $class) {
            if (!in_array($class, $output)) {
                $output = array_merge($output, $this->_buildInheritanceHierarchyTree($class, $allowedFiles));
                $output = array_unique($output);
            }
        }

        /** Convert data into data provider format */
        $outputClasses = [];
        foreach ($output as $className) {
            $outputClasses[] = [$className];
        }
        return $outputClasses;
    }

    /**
     * Build inheritance hierarchy tree
     *
     * @param string $className
     * @param array $allowedFiles
     * @return array
     */
    protected function _buildInheritanceHierarchyTree($className, array $allowedFiles)
    {
        $output = [];
        if (0 !== strpos($className, '\\')) {
            $className = '\\' . $className;
        }
        $class = new ReflectionClass($className);
        $parent = $class->getParentClass();
        $file = false;
        if ($parent) {
            $file = str_replace('\\', DIRECTORY_SEPARATOR, $parent->getFileName());
        }
        /** Prevent analysis of non Magento classes  */
        if ($parent && in_array($file, $allowedFiles)) {
            $output = array_merge(
                $this->_buildInheritanceHierarchyTree($parent->getName(), $allowedFiles),
                [$className],
                $output
            );
        } else {
            $output[] = $className;
        }
        return array_unique($output);
    }

    /**
     * Test consistency of plugin interfaces
     */
    public function testPluginInterfaces()
    {
        $invoker = new AggregateInvoker($this);
        $invoker(
            function ($plugin, $type) {
                $this->validatePlugins($plugin, $type);
            },
            $this->pluginDataProvider()
        );
    }

    /**
     * Validate plugin interface
     *
     * @param string $plugin
     * @param string $type
     */
    protected function validatePlugins($plugin, $type)
    {
        try {
            $module = Classes::getClassModuleName($type);
            if (Files::init()->isModuleExists($module)) {
                $this->pluginValidator->validate($plugin, $type);
            }
        } catch (ValidatorException $exception) {
            $this->fail($exception->getMessage());
        }
    }

    /**
     * Get application plugins
     *
     * @return array
     * @throws Exception
     */
    protected function pluginDataProvider()
    {
        $files = Files::init()->getDiConfigs();
        $plugins = [];
        foreach ($files as $file) {
            $dom = new DOMDocument();
            $dom->load($file);
            $xpath = new DOMXPath($dom);
            $pluginList = $xpath->query('//config/type/plugin');
            foreach ($pluginList as $node) {
                /** @var $node DOMNode */
                $type = $node->parentNode->attributes->getNamedItem('name')->nodeValue;
                $type = Classes::resolveVirtualType($type);
                if ($node->attributes->getNamedItem('type')) {
                    $plugin = $node->attributes->getNamedItem('type')->nodeValue;
                    if (!in_array($plugin, $this->getPluginBlacklist())) {
                        $plugin = Classes::resolveVirtualType($plugin);
                        $plugins[] = ['plugin' => $plugin, 'intercepted type' => $type];
                    }
                }
            }
        }

        return $plugins;
    }

    /**
     * Return plugin blacklist class names
     *
     * @return string[]
     */
    private function getPluginBlacklist(): array
    {
        if ($this->pluginBlacklist === null) {
            $blacklistFiles = str_replace(
                '\\',
                '/',
                realpath(__DIR__) . '/../_files/blacklist/compiler_plugins*.txt'
            );
            $blacklistItems = [];
            foreach (glob($blacklistFiles) as $fileName) {
                $blacklistItems = array_merge(
                    $blacklistItems,
                    file($fileName, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)
                );
            }
            $this->pluginBlacklist = $blacklistItems;
        }
        return $this->pluginBlacklist;
    }

    protected function setUp(): void
    {
        $this->_shell = new Shell(new CommandRenderer());
        $basePath = BP;
        $basePath = str_replace('\\', '/', $basePath);

        $directoryList = new DirectoryList($basePath);
        $this->_generationDir = $directoryList->getPath(DirectoryList::GENERATED_CODE);
        $this->_compilationDir = $directoryList->getPath(DirectoryList::GENERATED_METADATA);

        $this->_command = 'php ' . $basePath . '/bin/magento setup:di:compile';

        $booleanUtils = new BooleanUtils();
        $constInterpreter = new Constant();
        $argumentInterpreter = new Composite(
            [
                'boolean' => new Boolean($booleanUtils),
                'string' => new BaseStringUtils($booleanUtils),
                'number' => new Number(),
                'null' => new NullType(),
                'object' => new DataObject($booleanUtils),
                'const' => $constInterpreter,
                'init_parameter' => new ArgumentInterpreter($constInterpreter),
            ],
            \Magento\Framework\ObjectManager\Config\Reader\Dom::TYPE_ATTRIBUTE
        );
        // Add interpreters that reference the composite
        $argumentInterpreter->addInterpreter(
            'array',
            new ArrayType($argumentInterpreter)
        );

        $this->_mapper = new Dom(
            $argumentInterpreter,
            $booleanUtils,
            new ArgumentParser()
        );
        $this->_validator = new Validator();
        $this->_validator->add(new ConstructorIntegrity());
        $this->_validator->add(new TypeDuplication());
        $this->_validator->add(new ArgumentSequence());
        $this->_validator->add(new ConstructorArgumentTypes());
        $this->pluginValidator = new PluginValidator(new InterfaceValidator());
    }
}
