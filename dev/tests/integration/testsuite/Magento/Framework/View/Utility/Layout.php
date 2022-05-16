<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Core layout utility
 */

namespace Magento\Framework\View\Utility;

use Magento\Framework\App\State;
use Magento\Framework\Cache\FrontendInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Design\Theme\ResolverInterface;
use Magento\Framework\View\File\CollectorInterface;
use Magento\Framework\View\File\Factory;
use Magento\Framework\View\Layout\Data\Structure;
use Magento\Framework\View\Layout\Generator\ContextFactory;
use Magento\Framework\View\Layout\GeneratorPool;
use Magento\Framework\View\Layout\ProcessorFactory;
use Magento\Framework\View\Layout\ProcessorInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Psr\Log\LoggerInterface;

/**
 * Class Layout
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Layout
{
    /**
     * @var TestCase
     */
    protected $_testCase;

    /**
     * @param TestCase $testCase
     */
    public function __construct(TestCase $testCase)
    {
        $this->_testCase = $testCase;
    }

    /**
     * Retrieve new layout model instance with layout updates from a fixture file
     *
     * @param string|array $layoutUpdatesFile
     * @param array $args
     * @return \Magento\Framework\View\Layout|PHPUnit_Framework_MockObject_MockObject
     */
    public function getLayoutFromFixture($layoutUpdatesFile, array $args = [])
    {
        $layout = $this->_testCase->getMockBuilder(\Magento\Framework\View\Layout::class)
            ->setMethods(['getUpdate'])
            ->setConstructorArgs($args)
            ->getMock();
        $layoutUpdate = $this->getLayoutUpdateFromFixture($layoutUpdatesFile);
        $layoutUpdate->asSimplexml();
        $layout->expects(
            TestCase::any()
        )->method(
            'getUpdate'
        )->will(
            TestCase::returnValue($layoutUpdate)
        );
        return $layout;
    }

    /**
     * Retrieve new layout update model instance with XML data from a fixture file
     *
     * @param string|array $layoutUpdatesFile
     * @return ProcessorInterface
     */
    public function getLayoutUpdateFromFixture($layoutUpdatesFile)
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var Factory $fileFactory */
        $fileFactory = $objectManager->get(Factory::class);
        $files = [];
        foreach ((array)$layoutUpdatesFile as $filename) {
            $files[] = $fileFactory->create($filename, 'Magento_View');
        }
        $fileSource = $this->_testCase
            ->getMockBuilder(CollectorInterface::class)->getMockForAbstractClass();
        $fileSource->expects(
            TestCase::any()
        )->method(
            'getFiles'
        )->will(
            TestCase::returnValue($files)
        );
        $pageLayoutFileSource = $this->_testCase
            ->getMockBuilder(CollectorInterface::class)->getMockForAbstractClass();
        $pageLayoutFileSource->expects(TestCase::any())
            ->method('getFiles')
            ->willReturn([]);
        $cache = $this->_testCase
            ->getMockBuilder(FrontendInterface::class)->getMockForAbstractClass();
        return $objectManager->create(
            ProcessorInterface::class,
            ['fileSource' => $fileSource, 'pageLayoutFileSource' => $pageLayoutFileSource, 'cache' => $cache]
        );
    }

    /**
     * Retrieve object that will be used for layout instantiation
     *
     * @return array
     */
    public function getLayoutDependencies()
    {
        $objectManager = Bootstrap::getObjectManager();
        return [
            'processorFactory' => $objectManager->get(ProcessorFactory::class),
            'eventManager' => $objectManager->get(\Magento\Framework\Event\ManagerInterface::class),
            'structure' => $objectManager->create(Structure::class, []),
            'messageManager' => $objectManager->get(ManagerInterface::class),
            'themeResolver' => $objectManager->get(ResolverInterface::class),
            'reader' => $objectManager->get('commonRenderPool'),
            'generatorPool' => $objectManager->get(GeneratorPool::class),
            'cache' => $objectManager->get(\Magento\Framework\App\Cache\Type\Layout::class),
            'readerContextFactory' => $objectManager->get(\Magento\Framework\View\Layout\Reader\ContextFactory::class),
            'generatorContextFactory' => $objectManager->get(
                ContextFactory::class
            ),
            'appState' => $objectManager->get(State::class),
            'logger' => $objectManager->get(LoggerInterface::class),
        ];
    }
}
