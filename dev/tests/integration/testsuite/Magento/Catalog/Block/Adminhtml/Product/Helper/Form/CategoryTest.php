<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Block\Adminhtml\Product\Helper\Form;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\Authorization\Policy\DefaultPolicy;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\View\Layout;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    /**
     * @magentoAppArea Adminhtml
     */
    public function testGetAfterElementHtml()
    {
        $objectManager = Bootstrap::getObjectManager();
        $layout = Bootstrap::getObjectManager()->create(
            Layout::class,
            ['area' => FrontNameResolver::AREA_CODE]
        );
        $authorization = Bootstrap::getObjectManager()->create(
            AuthorizationInterface::class,
            ['aclPolicy' => new DefaultPolicy()]
        );

        $block = $objectManager->create(
            Category::class,
            ['layout' => $layout, 'authorization' => $authorization]
        );

        /** @var $formFactory FormFactory */
        $formFactory = $objectManager->get(FormFactory::class);
        $form = $formFactory->create();
        $block->setForm($form);

        $this->assertMatchesRegularExpression('/<button[^>]*New\sCategory[^>]*>/', $block->getAfterElementHtml());
    }
}
