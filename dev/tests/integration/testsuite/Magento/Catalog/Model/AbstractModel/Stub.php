<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Model\AbstractModel;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\AbstractModel;
use Magento\Catalog\Model\Product;

abstract class Stub extends AbstractModel implements ProductInterface
{
    /**
     * Retrieve Store Id
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->getData(Product::STORE_ID);
    }

    /**
     * Set product store id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this->setData(Product::STORE_ID, $storeId);
    }
}
