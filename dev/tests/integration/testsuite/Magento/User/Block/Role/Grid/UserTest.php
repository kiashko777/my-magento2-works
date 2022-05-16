<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\User\Block\Role\Grid;

use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\User\Model\ResourceModel\Role\User\Collection;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class UserTest extends TestCase
{
    /**
     * @var User
     */
    protected $_block;

    public function testPreparedCollection()
    {
        $this->_block->toHtml();
        $this->assertInstanceOf(
            Collection::class,
            $this->_block->getCollection()
        );
    }

    protected function setUp(): void
    {
        $layout = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        );
        $this->_block = $layout->createBlock(User::class);
    }
}
