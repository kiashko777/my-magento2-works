<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestModule2\Service\V1;

use Magento\TestModule2\Service\V1\Entity\Item;

interface NoWebApiXmlInterface
{
    /**
     * Get an item.
     *
     * @param int $id
     * @return Item
     */
    public function item($id);

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
     * Retrieve a list of items.
     *
     * @return Item[]
     */
    public function items();
}
