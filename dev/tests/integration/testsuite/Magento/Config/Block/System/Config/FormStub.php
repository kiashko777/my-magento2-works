<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Stub system config form block for integration test
 */

namespace Magento\Config\Block\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class FormStub extends Form
{
    /**
     * @var array
     */
    protected $_configDataStub = [];

    /**
     * @var array
     */
    protected $_configRootStub = [];

    /**
     * Sets stub config data
     *
     * @param array $configData
     */
    public function setStubConfigData(array $configData = [])
    {
        $this->_configDataStub = $configData;
    }

    /**
     * Sets stub config root
     *
     * @param array $configRoot
     * @return void
     */
    public function setStubConfigRoot(array $configRoot = [])
    {
        $this->_configRootStub = $configRoot;
    }

    /**
     * Initialize properties of object required for test.
     *
     * @return Form
     */
    protected function _initObjects()
    {
        $result = parent::_initObjects();
        $this->_configData = $this->_configDataStub;
        $this->_fieldRenderer = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Field::class
        );

        return $result;
    }
}
