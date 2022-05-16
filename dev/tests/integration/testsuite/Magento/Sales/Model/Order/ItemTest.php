<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Model\Order;

use Magento\Framework\App\ObjectManager;
use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
{
    /**
     * @param string $options
     * @param array $expectedData
     * @dataProvider getProductOptionsDataProvider
     */
    public function testGetProductOptions($options, $expectedData)
    {
        $model = ObjectManager::getInstance()->get(Item::class);
        $model->setData('product_options', $options);
        $this->assertEquals($expectedData, $model->getProductOptions());
    }

    /**
     * @return array
     */
    public function getProductOptionsDataProvider()
    {
        return [
            [
                '{"option1":1,"option2":2}',
                ["option1" => 1, "option2" => 2]
            ],
            [
                ["option1" => 1, "option2" => 2],
                ["option1" => 1, "option2" => 2]
            ],
        ];
    }
}
