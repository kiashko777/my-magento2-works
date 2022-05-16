<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestModule2\Service\V1;

use Magento\TestModule2\Service\V1\Entity\Item;

interface SubsetRestInterface
{
    /**
     * Return a single item.
     *
     * @param int $id
     * @return Item
     */
    public function item($id);

    /**
     * Return multiple items.
     *
     * @return Item[]
     */
    public function items();

    /**
     * Create an item.
     *
     * @param string $name
     * @return Item
     */
    public function create($name);

    /**
     * Update an item.
     *
     * @param Item $item
     * @return Item
     */
    public function update(Item $item);

    /**
     * Delete an item.
     *
     * @param int $id
     * @return Item
     */
    public function remove($id);
}
