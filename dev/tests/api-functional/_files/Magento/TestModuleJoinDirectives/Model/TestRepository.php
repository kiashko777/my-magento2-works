<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestModuleJoinDirectives\Model;

use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Quote\Api\Data\CartSearchResultsInterfaceFactory;
use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory;
use Magento\TestModuleJoinDirectives\Api\TestRepositoryInterface;

/**
 * Model TestRepository
 */
class TestRepository implements TestRepositoryInterface
{
    /**
     * @var CollectionFactory
     */
    private $quoteCollectionFactory;

    /**
     * @var CartSearchResultsInterfaceFactory
     */
    private $searchResultsDataFactory;

    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
     * @param CollectionFactory $quoteCollectionFactory
     * @param CartSearchResultsInterfaceFactory $searchResultsDataFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     */
    public function __construct(
        CollectionFactory $quoteCollectionFactory,
        CartSearchResultsInterfaceFactory  $searchResultsDataFactory,
        JoinProcessorInterface                                     $extensionAttributesJoinProcessor
    )
    {
        $this->quoteCollectionFactory = $quoteCollectionFactory;
        $this->searchResultsDataFactory = $searchResultsDataFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $quoteCollection = $this->quoteCollectionFactory->create();
        $this->extensionAttributesJoinProcessor->process($quoteCollection);
        $searchData = $this->searchResultsDataFactory->create();
        $searchData->setSearchCriteria($searchCriteria);
        $searchData->setItems($quoteCollection->getItems());
        return $searchData;
    }
}
