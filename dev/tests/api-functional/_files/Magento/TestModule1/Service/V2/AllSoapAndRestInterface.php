<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestModule1\Service\V2;

use Magento\Framework\Api\Filter;
use Magento\TestModule1\Service\V2\Entity\Item;

interface AllSoapAndRestInterface
{
    /**
     * Get item.
     *
     * @param int $id
     * @return Item
     */
    public function item($id);

    /**
     * Create item.
     *
     * @param string $name
     * @return Item
     */
    public function create($name);

    /**
     * Update item.
     *
     * @param Item $entityItem
     * @return Item
     */
    public function update(Item $entityItem);

    /**
     * Retrieve a list of items.
     *
     * @param Filter[] $filters
     * @param string $sortOrder
     * @return Item[]
     */
    public function items($filters = [], $sortOrder = 'ASC');

    /**
     * Delete an item.
     *
     * @param int $id
     * @return Item
     */
    public function delete($id);
}
