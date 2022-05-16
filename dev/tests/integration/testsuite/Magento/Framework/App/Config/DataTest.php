<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\App\Config;

use Magento\Config\Model\ResourceModel\Config\Data\Collection;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Config;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\ObjectManager;
use Magento\TestFramework\Entity;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    const SAMPLE_CONFIG_PATH = 'web/unsecure/base_url';

    const SAMPLE_VALUE = 'http://example.com/';

    /**
     * @var Value
     */
    protected $_model;

    public static function setUpBeforeClass(): void
    {
        Bootstrap::getObjectManager()->get(
            WriterInterface::class
        )->save(
            self::SAMPLE_CONFIG_PATH,
            self::SAMPLE_VALUE
        );
        self::_refreshConfiguration();
    }

    /**
     * Remove cached configuration and reinitialize the application
     */
    protected static function _refreshConfiguration()
    {
        Bootstrap::getObjectManager()->get(CacheInterface::class)
            ->clean([Config::CACHE_TAG]);
        Bootstrap::getInstance()->reinitialize();
        $appConfig = ObjectManager::getInstance()->get(Config::class);
        $appConfig->clean();
    }

    public static function tearDownAfterClass(): void
    {
        Bootstrap::getObjectManager()->get(
            WriterInterface::class
        )->delete(
            self::SAMPLE_CONFIG_PATH
        );
        self::_refreshConfiguration();
    }

    public function testIsValueChanged()
    {
        // load the model
        $collection = Bootstrap::getObjectManager()->create(
            Collection::class
        );
        $collection->addFieldToFilter(
            'path',
            self::SAMPLE_CONFIG_PATH
        )->addFieldToFilter(
            'scope_id',
            0
        )->addFieldToFilter(
            'scope',
            'default'
        );
        foreach ($collection as $configData) {
            $this->_model = $configData;
            break;
        }
        $this->assertNotEmpty($this->_model->getId());

        // assert
        $this->assertFalse($this->_model->isValueChanged());
        $this->_model->setValue(uniqid());
        $this->assertTrue($this->_model->isValueChanged());
    }

    public function testGetOldValue()
    {
        $this->_model->setPath(self::SAMPLE_CONFIG_PATH);
        $this->assertEquals(self::SAMPLE_VALUE, $this->_model->getOldValue());

        $this->_model->setWebsiteCode('base');
        $this->assertEquals(self::SAMPLE_VALUE, $this->_model->getOldValue());

        $this->_model->setStoreCode('default');
        $this->assertEquals(self::SAMPLE_VALUE, $this->_model->getOldValue());
    }

    public function testGetFieldsetDataValue()
    {
        $this->assertNull($this->_model->getFieldsetDataValue('key'));
        $this->_model->setFieldsetData(['key' => 'value']);
        $this->assertEquals('value', $this->_model->getFieldsetDataValue('key'));
    }

    public function testCRUD()
    {
        $this->_model->setData(
            ['scope' => 'default', 'scope_id' => 0, 'path' => 'test/config/path', 'value' => 'test value']
        );
        $crud = new Entity($this->_model, ['value' => 'new value']);
        $crud->testCrud();
    }

    public function testCollection()
    {
        $collection = $this->_model->getCollection();
        $collection->addScopeFilter(
            'test',
            0,
            'test'
        )->addPathFilter(
            'not_existing_path'
        )->addValueFilter(
            'not_existing_value'
        );
        $this->assertEmpty($collection->getItems());
    }

    protected function setUp(): void
    {
        $this->_model = Bootstrap::getObjectManager()->create(
            Value::class
        );
    }
}
