<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Webapi\Service\Entity;

use Magento\Framework\Webapi\Test\Unit\ServiceInputProcessor\AssociativeArray;
use Magento\Framework\Webapi\Test\Unit\ServiceInputProcessor\DataArray;
use Magento\Framework\Webapi\Test\Unit\ServiceInputProcessor\Nested;
use Magento\Framework\Webapi\Test\Unit\ServiceInputProcessor\Simple;
use Magento\Framework\Webapi\Test\Unit\ServiceInputProcessor\SimpleArray;

class TestService
{
    /**
     * @param int $entityId
     * @param string $name
     * @return string[]
     */
    public function simple($entityId, $name)
    {
        return [$entityId, $name];
    }

    /**
     * @param Nested $nested
     * @return Nested
     */
    public function nestedData(Nested $nested)
    {
        return $nested;
    }

    /**
     * @param int[] $ids
     * @return int[]
     */
    public function simpleArray(array $ids)
    {
        return $ids;
    }

    /**
     * @param string[] $associativeArray
     * @return string[]
     */
    public function associativeArray(array $associativeArray)
    {
        return $associativeArray;
    }

    /**
     * @param Simple[] $dataObjects
     * @return Simple[]
     */
    public function dataArray(array $dataObjects)
    {
        return $dataObjects;
    }

    /**
     * @param SimpleArray $arrayData
     * @return SimpleArray
     */
    public function nestedSimpleArray(SimpleArray $arrayData)
    {
        return $arrayData;
    }

    /**
     * @param AssociativeArray $associativeArrayData
     * @return AssociativeArray
     */
    public function nestedAssociativeArray(AssociativeArray $associativeArrayData)
    {
        return $associativeArrayData;
    }

    /**
     * @param DataArray $dataObjects
     * @return DataArray
     */
    public function nestedDataArray(DataArray $dataObjects)
    {
        return $dataObjects;
    }
}
