<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Config\Model\Config\Backend\Image;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Image\Adapter\AdapterInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class AdapterTest extends TestCase
{
    /**
     * @var Adapter
     */
    protected $_model = null;

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExceptionSave()
    {
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage(
            'The specified image adapter cannot be used because of: Image adapter for \'wrong\' is not setup.'
        );

        $this->_model->setValue('wrong')->save();
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testCorrectSave()
    {
        $this->_model->setValue(AdapterInterface::ADAPTER_GD2)->save();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->_model = Bootstrap::getObjectManager()->create(
            Adapter::class
        );
        $this->_model->setPath('path');
    }
}
