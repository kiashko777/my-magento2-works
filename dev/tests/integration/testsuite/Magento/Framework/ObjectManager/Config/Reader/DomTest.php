<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\ObjectManager\Config\Reader;

use DOMDocument;
use Magento\Framework\App\Arguments\FileResolver\Primary;
use Magento\Framework\App\Arguments\ValidationState;
use Magento\Framework\App\State;
use Magento\Framework\ObjectManager\Config\SchemaLocator;
use Magento\Framework\Phrase;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class DomTest @covers \Magento\Framework\ObjectManager\Config\Reader\Dom
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DomTest extends TestCase
{
    /**
     * @var Dom
     */
    protected $_model;

    /**
     * @var array
     */
    protected $_fileList;

    /**
     * @var Primary
     */
    protected $_fileResolverMock;

    /**
     * @var DOMDocument
     */
    protected $_mergedConfig;

    /**
     * @var ValidationState
     */
    protected $_validationState;

    /**
     * @var SchemaLocator
     */
    protected $_schemaLocator;

    /**
     * @var \Magento\Framework\ObjectManager\Config\Mapper\Dom
     */
    protected $_mapper;

    public function testRead()
    {
        $model = new Dom(
            $this->_fileResolverMock,
            $this->_mapper,
            $this->_schemaLocator,
            $this->_validationState
        );
        $this->assertEquals($this->_mapper->convert($this->_mergedConfig), $model->read('scope'));
    }

    protected function setUp(): void
    {
        $fixturePath = realpath(__DIR__ . '/../../_files') . '/';
        $this->_fileList = [
            file_get_contents($fixturePath . 'config_one.xml'),
            file_get_contents($fixturePath . 'config_two.xml'),
        ];

        $this->_fileResolverMock = $this->createMock(Primary::class);
        $this->_fileResolverMock->expects($this->once())->method('get')->willReturn($this->_fileList);

        /** @var Phrase\Renderer\Composite|MockObject $renderer */
        $renderer = $this->getMockBuilder(Phrase\Renderer\Composite::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** check arguments won't be translated for ObjectManager, even if has attribute 'translate'=true. */
        $renderer->expects(self::never())
            ->method('render');
        Phrase::setRenderer($renderer);

        $this->_mapper = Bootstrap::getObjectManager()->get(
            \Magento\Framework\ObjectManager\Config\Mapper\Dom::class
        );
        $this->_validationState = new ValidationState(
            State::MODE_DEFAULT
        );
        $this->_schemaLocator = new SchemaLocator();

        $this->_mergedConfig = new DOMDocument();
        $this->_mergedConfig->load($fixturePath . 'config_merged.xml');
    }
}
