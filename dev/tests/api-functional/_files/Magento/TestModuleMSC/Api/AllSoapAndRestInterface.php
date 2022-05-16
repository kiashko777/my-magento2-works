<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestModuleMSC\Api;

use Magento\TestModuleMSC\Api\Data\ItemInterface;

interface AllSoapAndRestInterface
{
    /**
     * @param int $itemId
     * @return ItemInterface
     */
    public function item($itemId);

    /**
     * @param string $name
     * @return ItemInterface
     */
    public function create($name);

    /**
     * @param ItemInterface $entityItem
     * @return ItemInterface
     */
    public function update(ItemInterface $entityItem);

    /**
     * @return ItemInterface[]
     */
    public function items();

    /**
     * @param string $name
     * @return ItemInterface
     */
    public function testOptionalParam($name = null);

    /**
     * @param ItemInterface $entityItem
     * @return ItemInterface
     */
    public function itemAnyType(ItemInterface $entityItem);

    /**
     * @return ItemInterface
     */
    public function getPreconfiguredItem();
}
