<?php
declare(strict_types=1);

namespace Devall\Customform\Api;

use Devall\Customform\Api\Data\CustomformInterface;
use Devall\Customform\Api\Data\CustomformSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

interface CustomformRepositoryInterface
{

    /**
     * Save Customform
     * @param CustomformInterface $customform
     * @return CustomformInterface
     * @throws LocalizedException
     */
    public function save(
        CustomformInterface $customform
    );

    /**
     * Retrieve Customform
     * @param string $customformId
     * @return CustomformInterface
     * @throws LocalizedException
     */
    public function get($customformId);

    /**
     * Retrieve Customform matching the specified criteria.
     * @param SearchCriteriaInterface $searchCriteria
     * @return CustomformSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Customform
     * @param CustomformInterface $customform
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(
        CustomformInterface $customform
    );

    /**
     * Delete Customform by ID
     * @param string $customformId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($customformId);
}
