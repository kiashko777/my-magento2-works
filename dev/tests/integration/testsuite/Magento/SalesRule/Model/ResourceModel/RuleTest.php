<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\SalesRule\Model\ResourceModel;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class RuleTest extends TestCase
{
    /**
     * @magentoDataFixture Magento/SalesRule/_files/rule_custom_product_attribute.php
     */
    public function testAfterSave()
    {
        $resource = Bootstrap::getObjectManager()->create(
            Rule::class
        );
        $items = $resource->getActiveAttributes();

        $this->assertEquals([['attribute_code' => 'attribute_for_sales_rule_1']], $items);
    }
}
