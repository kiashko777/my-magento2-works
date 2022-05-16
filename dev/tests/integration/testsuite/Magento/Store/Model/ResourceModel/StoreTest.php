<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Store\Model\ResourceModel;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class StoreTest extends TestCase
{
    public function testCountAll()
    {
        /** @var $model Store */
        $model = Bootstrap::getObjectManager()->create(
            Store::class
        );
        $this->assertEquals(1, $model->countAll());
        $this->assertEquals(1, $model->countAll(false));
        $this->assertEquals(2, $model->countAll(true));
    }
}
