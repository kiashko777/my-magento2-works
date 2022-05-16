<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Block\Widget;

use Magento\Customer\Model\Attribute;
use Magento\Customer\Model\Data\Option;
use Magento\Framework\App\State;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea frontend
 */
class GenderTest extends TestCase
{
    /** @var Gender */
    protected $_block;

    /** @var Attribute */
    private $_model;

    /**
     * Test the Gender::getGenderOptions() method.
     * @return void
     */
    public function testGetGenderOptions()
    {
        $options = $this->_block->getGenderOptions();
        $this->assertIsArray($options);
        $this->assertNotEmpty($options);
        $this->assertContainsOnlyInstancesOf(Option::class, $options);
    }

    /**
     * Test the Gender::toHtml() method.
     * @return void
     */
    public function testToHtml()
    {
        $html = $this->_block->toHtml();
        $attributeLabel = $this->_model->getStoreLabel();
        $this->assertStringContainsString('<span>' . $attributeLabel . '</span>', $html);
        $this->assertStringContainsString('<option value="1">Male</option>', $html);
        $this->assertStringContainsString('<option value="2">Female</option>', $html);
        $this->assertStringContainsString('<option value="3">Not Specified</option>', $html);
    }

    /**
     * Test initialization and set up. Create the Gender block.
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get(State::class)->setAreaCode('frontend');
        $this->_block = $objectManager->get(
            LayoutInterface::class
        )->createBlock(
            Gender::class
        );
        $this->_model = $objectManager->create(Attribute::class);
        $this->_model->loadByCode('customer', 'gender');
    }
}
