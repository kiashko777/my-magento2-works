<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\PageCache\Model\System\Config\Backend;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class TtlTest extends TestCase
{
    /**
     * @var Ttl
     */
    protected $_model;

    /**
     * @var ScopeConfigInterface
     */
    protected $_config;

    /**
     * @dataProvider beforeSaveDataProvider
     *
     * @param $value
     * @param $path
     */
    public function testBeforeSave($value, $path)
    {
        $this->_prepareData($value, $path);
    }

    /**
     * @param $value
     * @param $path
     */
    protected function _prepareData($value, $path)
    {
        $this->_model->setValue($value);
        $this->_model->setPath($path);
        $this->_model->setField($path);
        $this->_model->save();
    }

    public function beforeSaveDataProvider()
    {
        return [
            [125, 'ttl_1'],
            [0, 'ttl_2'],
        ];
    }

    /**
     * @dataProvider beforeSaveDataProviderWithException
     *
     * @param $value
     * @param $path
     */
    public function testBeforeSaveWithException($value, $path)
    {
        $this->expectException(LocalizedException::class);
        $this->_prepareData($value, $path);
    }

    public function beforeSaveDataProviderWithException()
    {
        return [
            ['', 'ttl_3'],
            ['sdfg', 'ttl_4']
        ];
    }

    protected function setUp(): void
    {
        $this->_config = Bootstrap::getObjectManager()
            ->create(ScopeConfigInterface::class);
        $this->_model = Bootstrap::getObjectManager()
            ->create(Ttl::class);
    }
}
