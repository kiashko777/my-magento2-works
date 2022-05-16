<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Api\ExtensionAttribute\Config;

use Magento\Catalog\Api\Data\Product;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\Arguments\FileResolver\Primary;
use Magento\Framework\App\Arguments\ValidationState;
use Magento\Framework\App\State;
use Magento\Framework\Config\Dom\UrnResolver;
use Magento\Tax\Api\Data\TaxRateInterface;
use PHPUnit\Framework\TestCase;

/**
 * Tests for \Magento\Framework\Api\ExtensionAttribute\Config\Reader
 */
class ReaderTest extends TestCase
{
    /**
     * @var Reader
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
     * @var ValidationState
     */
    protected $_validationState;

    /**
     * @var SchemaLocator
     */
    protected $_schemaLocator;

    /**
     * @var Converter
     */
    protected $_converter;

    public function testMerge()
    {
        $model = new Reader(
            $this->_fileResolverMock,
            $this->_converter,
            $this->_schemaLocator,
            $this->_validationState
        );

        $expectedArray = [
            TaxRateInterface::class => [],
            Product::class => [
                'stock_item' => [
                    "type" => "Magento\CatalogInventory\Api\Data\StockItem",
                    "resourceRefs" => [],
                    "join" => null,
                ],
            ],
            CustomerInterface::class => [
                'custom_1' => [
                    "type" => "Magento\Customer\Api\Data\CustomerCustom",
                    "resourceRefs" => [],
                    "join" => null,
                ],
                'custom_2' => [
                    "type" => "Magento\CustomerExtra\Api\Data\CustomerCustom22",
                    "resourceRefs" => [],
                    "join" => null,
                ],
                'custom_3' => [
                    "type" => "Magento\Customer\Api\Data\CustomerCustom3",
                    "resourceRefs" => [],
                    "join" => null,
                ],
            ],
        ];

        $this->assertEquals($expectedArray, $model->read('global'));
    }

    protected function setUp(): void
    {
        $fixturePath = realpath(__DIR__ . '/_files') . '/';
        $this->_fileList = [
            file_get_contents($fixturePath . 'config_one.xml'),
            file_get_contents($fixturePath . 'config_two.xml'),
        ];

        $this->_fileResolverMock = $this->getMockBuilder(Primary::class)
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock();
        $this->_fileResolverMock->expects($this->once())
            ->method('get')
            ->willReturn($this->_fileList);

        $this->_converter = new Converter();

        $this->_validationState = new ValidationState(
            State::MODE_DEFAULT
        );
        $this->_schemaLocator = new SchemaLocator(
            new UrnResolver()
        );
    }
}
