<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test for eav abstract export model
 */

namespace Magento\ImportExport\Model\Export\Entity;

use Magento\Customer\Model\Attribute;
use Magento\Customer\Model\ResourceModel\Attribute\Collection;
use Magento\Eav\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\ImportExport\Model\Export;
use Magento\ImportExport\Model\ResourceModel\CollectionByPagesIteratorFactory;
use Magento\Store\Model\StoreManager;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class AbstractEavTest extends TestCase
{
    /**
     * Skipped attribute codes
     *
     * @var array
     */
    protected static $_skippedAttributes = ['confirmation', 'lastname'];

    /**
     * @var AbstractEav
     */
    protected $_model;

    /**
     * Entity code
     *
     * @var string
     */
    protected $_entityCode = 'customer';

    /**
     * Test for method getEntityTypeId()
     */
    public function testGetEntityTypeId()
    {
        $entityCode = 'customer';
        $entityId = Bootstrap::getObjectManager()->get(
            Config::class
        )->getEntityType(
            $entityCode
        )->getEntityTypeId();

        $this->assertEquals($entityId, $this->_model->getEntityTypeId());
    }

    /**
     * Test for method _getExportAttrCodes()
     *
     * @covers \Magento\ImportExport\Model\Export\Entity\AbstractEav::_getExportAttributeCodes
     */
    public function testGetExportAttrCodes()
    {
        $this->_model->setParameters($this->_getSkippedAttributes());
        $method = new ReflectionMethod($this->_model, '_getExportAttributeCodes');
        $method->setAccessible(true);
        $attributes = $method->invoke($this->_model);
        foreach (self::$_skippedAttributes as $code) {
            $this->assertNotContains($code, $attributes);
        }
    }

    /**
     * Retrieve list of skipped attributes
     *
     * @return array
     */
    protected function _getSkippedAttributes()
    {
        /** @var $attributeCollection Collection */
        $attributeCollection = Bootstrap::getObjectManager()->create(
            Collection::class
        );
        $attributeCollection->addFieldToFilter('attribute_code', ['in' => self::$_skippedAttributes]);
        $skippedAttributes = [];
        /** @var $attribute  Attribute */
        foreach ($attributeCollection as $attribute) {
            $skippedAttributes[$attribute->getAttributeCode()] = $attribute->getId();
        }

        return [Export::FILTER_ELEMENT_SKIP => $skippedAttributes];
    }

    /**
     * Test for method getAttributeOptions()
     */
    public function testGetAttributeOptions()
    {
        /** @var $attributeCollection Collection */
        $attributeCollection = Bootstrap::getObjectManager()->create(
            Collection::class
        );
        $attributeCollection->addFieldToFilter('attribute_code', 'gender');
        /** @var $attribute Attribute */
        $attribute = $attributeCollection->getFirstItem();

        $expectedOptions = [];
        foreach ($attribute->getSource()->getAllOptions(false) as $option) {
            $expectedOptions[$option['value']] = $option['label'];
        }

        $actualOptions = $this->_model->getAttributeOptions($attribute);
        $this->assertEquals($expectedOptions, $actualOptions);
    }

    protected function setUp(): void
    {
        /** @var ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();

        $customerAttributes = Bootstrap::getObjectManager()->create(
            Collection::class
        );

        $this->_model = $this->getMockBuilder(AbstractEav::class)
            ->setMethods(['getEntityTypeCode', 'getAttributeCollection'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->_model->expects(
            $this->any()
        )->method(
            'getEntityTypeCode'
        )->willReturn(
            $this->_entityCode
        );
        $this->_model->expects(
            $this->any()
        )->method(
            'getAttributeCollection'
        )->willReturn(
            $customerAttributes
        );
        $this->_model->__construct(
            $objectManager->get(ScopeConfigInterface::class),
            $objectManager->get(StoreManager::class),
            $objectManager->get(\Magento\ImportExport\Model\Export\Factory::class),
            $objectManager->get(CollectionByPagesIteratorFactory::class),
            $objectManager->get(TimezoneInterface::class),
            $objectManager->get(Config::class)
        );
    }
}
