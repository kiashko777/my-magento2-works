<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Variable\Block\System\Variable;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\Variable\Model\Variable;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class EditTest extends TestCase
{
    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     */
    public function testConstruct()
    {
        $data = [
            'code' => 'test_variable_1',
            'name' => 'Test Variable 1',
            'html_value' => '<b>Test Variable 1 HTML Value</b>',
            'plain_value' => 'Test Variable 1 plain Value',
        ];
        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        $variable = $objectManager->create(Variable::class)->setData($data)->save();

        $objectManager->get(Registry::class)->register('current_variable', $variable);
        $objectManager->get(
            RequestInterface::class
        )->setParam('variable_id', $variable->getId());
        $block = $objectManager->get(
            LayoutInterface::class
        )->createBlock(
            Edit::class,
            'variable'
        );
        $this->assertArrayHasKey('variable-delete_button', $block->getLayout()->getAllBlocks());
    }
}
