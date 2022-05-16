<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test class for \Magento\Catalog\Controller\index.
 */

namespace Magento\Catalog\Controller;

use Magento\TestFramework\TestCase\AbstractController;

class IndexTest extends AbstractController
{
    public function testIndexAction()
    {
        $this->dispatch('catalog/index');

        $this->assertRedirect();
    }
}
