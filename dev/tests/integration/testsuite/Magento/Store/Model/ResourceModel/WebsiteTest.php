<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Store\Model\ResourceModel;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class WebsiteTest extends TestCase
{
    public function testCountAll()
    {
        /** @var $model Website */
        $model = Bootstrap::getObjectManager()->create(
            Website::class
        );
        $this->assertEquals(1, $model->countAll());
        $this->assertEquals(1, $model->countAll(false));
        $this->assertEquals(2, $model->countAll(true));
    }
}
