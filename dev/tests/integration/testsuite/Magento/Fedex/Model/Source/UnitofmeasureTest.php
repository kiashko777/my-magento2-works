<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Fedex\Model\Source;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class UnitofmeasureTest extends TestCase
{
    public function testToOptionArray()
    {
        /** @var $model Unitofmeasure */
        $model = Bootstrap::getObjectManager()->create(
            Unitofmeasure::class
        );
        $result = $model->toOptionArray();
        $this->assertCount(2, $result);
    }
}
