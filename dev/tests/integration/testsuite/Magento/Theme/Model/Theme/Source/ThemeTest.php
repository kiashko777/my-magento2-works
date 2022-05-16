<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Theme\Model\Theme\Source;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\Theme\Model\ResourceModel\Theme\Collection;
use PHPUnit\Framework\TestCase;

/**
 * Theme Test
 *
 */
class ThemeTest extends TestCase
{
    public function testGetAllOptions()
    {
        /** @var $model Theme */
        $model = Bootstrap::getObjectManager()->create(Theme::class);

        /** @var $expectedCollection \Magento\Theme\Model\Theme\Collection */
        $expectedCollection = Bootstrap::getObjectManager()
            ->create(Collection::class);
        $expectedCollection->addFilter('area', 'frontend');

        $expectedItemsCount = count($expectedCollection);

        $labelsCollection = $model->getAllOptions(false);
        $this->assertEquals($expectedItemsCount, count($labelsCollection));

        $labelsCollection = $model->getAllOptions(true);
        $this->assertEquals(++$expectedItemsCount, count($labelsCollection));
    }
}
