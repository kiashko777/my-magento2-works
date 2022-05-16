<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestModule1\Service\V1;

use Magento\TestModule1\Service\V1\Entity\Item;

interface AllSoapAndRestInterface
{
    /**
     * @param int $itemId
     * @return Item
     */
    public function item($itemId);

    /**
     * @param string $name
     * @return Item
     */
    public function create($name);

    /**
     * @param Item $entityItem
     * @return Item
     */
    public function update(Item $entityItem);

    /**
     * @return Item[]
     */
    public function items();

    /**
     * @param string $name
     * @return Item
     */
    public function testOptionalParam($name = null);

    /**
     * @param Item $entityItem
     * @return Item
     */
    public function itemAnyType(Item $entityItem);

    /**
     * @return Item
     */
    public function getPreconfiguredItem();
}
