<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestModule4\Service\V1;

use Magento\TestModule4\Service\V1\Entity\DataObjectRequest;
use Magento\TestModule4\Service\V1\Entity\DataObjectResponse;
use Magento\TestModule4\Service\V1\Entity\ExtensibleRequestInterface;
use Magento\TestModule4\Service\V1\Entity\NestedDataObjectRequest;

interface DataObjectServiceInterface
{
    /**
     * @param int $id
     * @return DataObjectResponse
     */
    public function getData($id);

    /**
     * @param int $id
     * @param DataObjectRequest $request
     * @return DataObjectResponse
     */
    public function updateData($id, DataObjectRequest $request);

    /**
     * @param int $id
     * @param NestedDataObjectRequest $request
     * @return DataObjectResponse
     */
    public function nestedData($id, NestedDataObjectRequest $request);

    /**
     * Test return scalar value
     *
     * @param int $id
     * @return int
     */
    public function scalarResponse($id);

    /**
     * @param int $id
     * @param ExtensibleRequestInterface $request
     * @return DataObjectResponse
     */
    public function extensibleDataObject(
        $id,
        ExtensibleRequestInterface $request
    );
}
