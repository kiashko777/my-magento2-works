<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test class for \Magento\Sales\Block\Adminhtml\Order\Create\Form\AbstractForm
 */

namespace Magento\Sales\Block\Adminhtml\Order\Create\Form;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Session\Quote;
use Magento\Customer\Api\Data\AttributeMetadataInterfaceFactory;
use Magento\Customer\Api\Data\OptionInterfaceFactory;
use Magento\Customer\Api\Data\ValidationRuleInterfaceFactory;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\Layout;
use Magento\Sales\Model\AdminOrder\Create;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * Class AbstractTest
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AbstractTest extends TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testAddAttributesToForm()
    {
        $objectManager = Bootstrap::getObjectManager();
        Bootstrap::getInstance()
            ->loadArea(FrontNameResolver::AREA_CODE);

        $objectManager->get(DesignInterface::class)->setDefaultDesignTheme();
        $arguments = [
            $objectManager->get(Context::class),
            $objectManager->get(Quote::class),
            $objectManager->get(Create::class),
            $objectManager->get(PriceCurrencyInterface::class),
            $objectManager->get(FormFactory::class),
            $objectManager->get(DataObjectProcessor::class)
        ];

        /** @var $block AbstractForm */
        $block = $this->getMockForAbstractClass(
            AbstractForm::class,
            $arguments
        );
        $block->setLayout($objectManager->create(Layout::class));

        $method = new ReflectionMethod(
            AbstractForm::class,
            '_addAttributesToForm'
        );
        $method->setAccessible(true);

        /** @var $formFactory FormFactory */
        $formFactory = $objectManager->get(FormFactory::class);
        $form = $formFactory->create();
        $fieldset = $form->addFieldset('test_fieldset', []);
        /** @var AttributeMetadataInterfaceFactory $attributeMetadataFactory */
        $attributeMetadataFactory =
            $objectManager->create(AttributeMetadataInterfaceFactory::class);
        $dateAttribute = $attributeMetadataFactory->create()->setAttributeCode('date')
            ->setBackendType('datetime')
            ->setFrontendInput('date')
            ->setFrontendLabel('Date');
        $attributes = ['date' => $dateAttribute];
        $method->invoke($block, $attributes, $fieldset);

        $element = $form->getElement('date');
        $this->assertNotNull($element);
        $this->assertNotEmpty($element->getDateFormat());
    }
}
