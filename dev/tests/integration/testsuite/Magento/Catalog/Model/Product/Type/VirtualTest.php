<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Model\Product\Type;

use Magento\Catalog\Model\Product;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class VirtualTest extends TestCase
{
    public function testIsVirtual()
    {
        /** @var $model Virtual */
        $model = Bootstrap::getObjectManager()->create(
            Virtual::class
        );
        $product = Bootstrap::getObjectManager()->create(
            Product::class
        );
        $this->assertTrue($model->isVirtual($product));
    }
}
