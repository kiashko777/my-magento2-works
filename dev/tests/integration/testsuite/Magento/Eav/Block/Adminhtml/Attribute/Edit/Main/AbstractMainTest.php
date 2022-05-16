<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test class for \Magento\Eav\Block\Adminhtml\Attribute\Edit\Main\AbstractMain
 */

namespace Magento\Eav\Block\Adminhtml\Attribute\Edit\Main;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Model\Config\Source\YesnoFactory;
use Magento\Customer\Model\Attribute;
use Magento\Eav\Block\Adminhtml\Attribute\PropertyLocker;
use Magento\Eav\Helper\Data;
use Magento\Eav\Model\Adminhtml\System\Config\Source\InputtypeFactory;
use Magento\Eav\Model\Config;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\Layout;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AbstractMainTest extends TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();

        Bootstrap::getInstance()
            ->loadArea(FrontNameResolver::AREA_CODE);
        $objectManager->get(DesignInterface::class)
            ->setDefaultDesignTheme();
        $entityType = Bootstrap::getObjectManager()->get(Config::class)
            ->getEntityType('customer');
        $model = $objectManager->create(Attribute::class);
        $model->setEntityTypeId($entityType->getId());
        $objectManager->get(Registry::class)->register('entity_attribute', $model);

        $block = $this->getMockForAbstractClass(
            AbstractMain::class,
            [
                $objectManager->get(Context::class),
                $objectManager->get(Registry::class),
                $objectManager->get(FormFactory::class),
                $objectManager->get(Data::class),
                $objectManager->get(YesnoFactory::class),
                $objectManager->get(InputtypeFactory::class),
                $objectManager->get(PropertyLocker::class)
            ]
        )->setLayout(
            $objectManager->create(Layout::class)
        );

        $method = new ReflectionMethod(
            AbstractMain::class,
            '_prepareForm'
        );
        $method->setAccessible(true);
        $method->invoke($block);

        $element = $block->getForm()->getElement('default_value_date');
        $this->assertNotNull($element);
        $this->assertNotEmpty($element->getDateFormat());
    }
}
