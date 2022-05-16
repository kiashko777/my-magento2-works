<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\View\Element;

use Magento\Framework\App\Area;
use Magento\Framework\DataObject;
use Magento\Framework\View\Layout;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\App\State;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

class TemplateTest extends TestCase
{
    /**
     * @var Template
     */
    protected $_block;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    private $origMode;

    public function testConstruct()
    {
        $block = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Template::class,
            '',
            ['data' => ['template' => 'value']]
        );
        $this->assertEquals('value', $block->getTemplate());
    }

    public function testSetGetTemplate()
    {
        $this->assertEmpty($this->_block->getTemplate());
        $this->_block->setTemplate('value');
        $this->assertEquals('value', $this->_block->getTemplate());
    }

    public function testGetArea()
    {
        Bootstrap::getObjectManager()->get(\Magento\Framework\App\State::class)
            ->setAreaCode('frontend');
        $this->assertEquals('frontend', $this->_block->getArea());
        Bootstrap::getObjectManager()->get(
            \Magento\Framework\App\State::class
        )->setAreaCode(Area::AREA_ADMINHTML);
        $this->assertEquals(Area::AREA_ADMINHTML, $this->_block->getArea());
        Bootstrap::getObjectManager()->get(
            \Magento\Framework\App\State::class
        )->setAreaCode(Area::AREA_GLOBAL);
        $this->assertEquals(Area::AREA_GLOBAL, $this->_block->getArea());
    }

    /**
     * @covers \Magento\Framework\View\Element\AbstractBlock::toHtml
     * @see    testAssign()
     */
    public function testToHtml()
    {
        Bootstrap::getObjectManager()->get(\Magento\Framework\App\State::class)
            ->setAreaCode(Area::AREA_GLOBAL);
        /** @var State $appState */
        $appState = $this->objectManager->get(State::class);
        $appState->setMode(State::MODE_DEFAULT);
        $this->assertEmpty($this->_block->toHtml());
        $this->_block->setTemplate(uniqid('invalid_filename.phtml'));
        $this->assertEmpty($this->_block->toHtml());
    }

    public function testGetBaseUrl()
    {
        $this->assertEquals('http://localhost/index.php/', $this->_block->getBaseUrl());
    }

    public function testGetObjectData()
    {
        $object = new DataObject(['key' => 'value']);
        $this->assertEquals('value', $this->_block->getObjectData($object, 'key'));
    }

    public function testGetCacheKeyInfo()
    {
        $this->assertArrayHasKey('template', $this->_block->getCacheKeyInfo());
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $params = ['layout' => $this->objectManager->create(Layout::class, [])];
        $context = $this->objectManager->create(\Magento\Framework\View\Element\Template\Context::class, $params);
        $this->_block = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Template::class,
            '',
            ['context' => $context]
        );

        /** @var State $appState */
        $appState = $this->objectManager->get(State::class);
        $this->origMode = $appState->getMode();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown(): void
    {
        /** @var State $appState */
        $appState = $this->objectManager->get(State::class);
        $appState->setMode($this->origMode);
        parent::tearDown();
    }
}
