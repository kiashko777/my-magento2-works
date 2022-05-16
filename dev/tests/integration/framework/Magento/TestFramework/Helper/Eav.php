<?php
/**
 * Helper for EAV functionality in integration tests.
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestFramework\Helper;

use Magento\Eav\Model\Entity\Store;
use Magento\Eav\Model\Entity\Type;
use Magento\Store\Model\StoreManagerInterface;

class Eav
{
    /**
     * Set increment id prefix in entity model.
     *
     * @param string $entityType
     * @param string $prefix
     */
    public static function setIncrementIdPrefix($entityType, $prefix)
    {
        $website = Bootstrap::getObjectManager()->get(
            StoreManagerInterface::class
        )->getWebsite();
        $storeId = $website->getDefaultStore()->getId();
        $entityTypeModel = Bootstrap::getObjectManager()->create(
            Type::class
        )->loadByCode(
            $entityType
        );
        /** @var Store $entityStore */
        $entityStore = Bootstrap::getObjectManager()->create(
            Store::class
        )->loadByEntityStore(
            $entityTypeModel->getId(),
            $storeId
        );
        $entityStore->setEntityTypeId($entityTypeModel->getId());
        $entityStore->setStoreId($storeId);
        $entityStore->setIncrementPrefix($prefix);
        $entityStore->save();
    }
}
