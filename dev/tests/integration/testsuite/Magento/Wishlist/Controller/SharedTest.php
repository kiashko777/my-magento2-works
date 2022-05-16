<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Wishlist\Controller;

use Magento\Checkout\Model\Cart;
use Magento\TestFramework\TestCase\AbstractController;

class SharedTest extends AbstractController
{
    /**
     * @magentoDataFixture Magento/Wishlist/_files/wishlist_shared.php
     * @magentoDbIsolation disabled
     * @return void
     */
    public function testAllcartAction()
    {
        $this->getRequest()->setParam('code', 'fixture_unique_code');
        $this->dispatch('wishlist/shared/allcart');

        /** @var Cart $cart */
        $cart = $this->_objectManager->get(Cart::class);
        $quoteCount = $cart->getQuote()->getItemsCollection()->count();

        $this->assertEquals(1, $quoteCount);
    }
}
