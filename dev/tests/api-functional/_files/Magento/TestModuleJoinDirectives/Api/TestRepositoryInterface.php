<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestModuleJoinDirectives\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Quote\Api\Data\CartSearchResultsInterface;

/**
 * Interface TestRepositoryInterface
 */
interface TestRepositoryInterface
{
    /**
     * Get list of quotes
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return CartSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
