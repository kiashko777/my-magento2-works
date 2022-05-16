<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test for abstract export model
 */

namespace Magento\ImportExport\Model\Export;

use Magento\Customer\Model\Attribute;
use Magento\Customer\Model\ResourceModel\Attribute\Collection;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\ImportExport\Model\Export\Adapter\Csv;
use Magento\ImportExport\Model\ResourceModel\CollectionByPagesIteratorFactory;
use Magento\Store\Model\StoreManager;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

class EntityAbstractTest extends TestCase
{
    /**
     * @var AbstractEntity
     */
    protected $_model;

    /**
     * Check methods which provide ability to manage errors
     */
    public function testAddRowError()
    {
        $errorCode = 'test_error';
        $errorNum = 1;
        $errorMessage = 'Test error!';
        $this->_model->addMessageTemplate($errorCode, $errorMessage);
        $this->_model->addRowError($errorCode, $errorNum);

        $this->assertEquals(1, $this->_model->getErrorsCount());
        $this->assertEquals(1, $this->_model->getInvalidRowsCount());
        $this->assertArrayHasKey($errorMessage, $this->_model->getErrorMessages());
    }

    /**
     * Check methods which provide ability to manage writer object
     */
    public function testGetWriter()
    {
        $this->_model->setWriter(
            Bootstrap::getObjectManager()->create(
                Csv::class
            )
        );
        $this->assertInstanceOf(Csv::class, $this->_model->getWriter());
    }

    /**
     * Check that method throw exception when writer was not defined
     *
     */
    public function testGetWriterThrowsException()
    {
        $this->expectException(LocalizedException::class);

        $this->_model->getWriter();
    }

    /**
     * Test for method filterAttributeCollection
     */
    public function testFilterAttributeCollection()
    {
        $collection = Bootstrap::getObjectManager()->create(
            Collection::class
        );
        $collection = $this->_model->filterAttributeCollection($collection);
        /**
         * Check that disabled attributes is not existed in attribute collection
         */
        $existedAttributes = [];
        /** @var $attribute Attribute */
        foreach ($collection as $attribute) {
            $existedAttributes[] = $attribute->getAttributeCode();
        }
        $disabledAttributes = $this->_model->getDisabledAttributes();
        foreach ($disabledAttributes as $attributeCode) {
            $this->assertNotContains(
                $attributeCode,
                $existedAttributes,
                'Disabled attribute "' . $attributeCode . '" existed in collection'
            );
        }
    }

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @var ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();
        $this->_model = $this->getMockForAbstractClass(
            AbstractEntity::class,
            [
                $objectManager->get(ScopeConfigInterface::class),
                $objectManager->get(StoreManager::class),
                $objectManager->get(Factory::class),
                $objectManager->get(CollectionByPagesIteratorFactory::class)
            ]
        );
    }
}
