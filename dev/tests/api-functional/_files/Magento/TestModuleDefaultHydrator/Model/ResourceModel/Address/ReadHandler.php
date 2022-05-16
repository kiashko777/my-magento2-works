<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestModuleDefaultHydrator\Model\ResourceModel\Address;

use Exception;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

class ReadHandler implements ExtensionInterface
{
    /**
     * @var EntityManager
     */
    private $addressRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param AddressRepositoryInterface $addressRepositoryInterface
     * @param SearchCriteriaBuilder $SearchCriteriaBuilder
     */
    public function __construct(
        AddressRepositoryInterface $addressRepository,
        SearchCriteriaBuilder      $searchCriteriaBuilder
    )
    {
        $this->addressRepository = $addressRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param CustomerInterface $entity
     * @return CustomerInterface
     * @throws Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('parent_id', $entity->getId())
            ->create();
        $addressesResult = $this->addressRepository->getList($searchCriteria);
        $entity->setAddresses($addressesResult->getItems());
        return $entity;
    }
}
