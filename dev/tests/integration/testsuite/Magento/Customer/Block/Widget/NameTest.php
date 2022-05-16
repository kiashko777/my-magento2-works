<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Block\Widget;

use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\App\State;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test \Magento\Customer\Block\Widget\Name
 * @magentoAppArea frontend
 */
class NameTest extends TestCase
{
    /** @var Name */
    protected $_block;

    /**
     * @magentoAppIsolation enabled
     */
    public function testToHtmlSimpleName()
    {
        /** @var CustomerInterfaceFactory $customerFactory */
        $customerFactory = Bootstrap::getObjectManager()->get(
            CustomerInterfaceFactory::class
        );
        $customerDataObject = $customerFactory->create();
        $customerDataObject->setFirstname('Jane');
        $customerDataObject->setLastname('Doe');
        $this->_block->setObject($customerDataObject);

        $html = $this->_block->toHtml();

        $this->assertStringContainsString('title="First&#x20;Name"', $html);
        $this->assertStringContainsString('value="Jane"', $html);
        $this->assertStringContainsString('title="Last&#x20;Name"', $html);
        $this->assertStringContainsString('value="Doe"', $html);
        $this->assertStringNotContainsString('title="Middle&#x20;Name&#x2F;Initial"', $html);
        $this->assertStringNotContainsString('title="Name&#x20;Prefix"', $html);
        $this->assertStringNotContainsString('title="Name&#x20;Suffix"', $html);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Customer/_files/attribute_user_fullname.php
     */
    public function testToHtmlFancyName()
    {
        /** @var CustomerInterfaceFactory $customerFactory */
        $customerFactory = Bootstrap::getObjectManager()->get(
            CustomerInterfaceFactory::class
        );
        $customerDataObject = $customerFactory->create();
        $customerDataObject->setPrefix(
            'Dr.'
        )->setFirstname(
            'Jane'
        )->setMiddlename(
            'Roe'
        )->setLastname(
            'Doe'
        )->setSuffix(
            'Ph.D.'
        );
        $this->_block->setObject($customerDataObject);

        $html = $this->_block->toHtml();

        $this->assertStringContainsString('title="First&#x20;Name"', $html);
        $this->assertStringContainsString('value="Jane"', $html);
        $this->assertStringContainsString('title="Last&#x20;Name"', $html);
        $this->assertStringContainsString('value="Doe"', $html);
        $this->assertStringContainsString('title="Middle&#x20;Name&#x2F;Initial"', $html);
        $this->assertStringContainsString('value="Roe"', $html);
        $this->assertStringContainsString('title="Name&#x20;Prefix"', $html);
        $this->assertStringContainsString('value="Dr."', $html);
        $this->assertStringContainsString('title="Name&#x20;Suffix"', $html);
        $this->assertStringContainsString('value="Ph.D."', $html);
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get(State::class)->setAreaCode('frontend');
        $this->_block = $objectManager->get(
            LayoutInterface::class
        )->createBlock(
            Name::class
        );
    }
}
