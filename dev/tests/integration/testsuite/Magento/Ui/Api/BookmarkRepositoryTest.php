<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Ui\Api;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterface;
use Magento\Ui\Model\ResourceModel\BookmarkRepository;
use PHPUnit\Framework\TestCase;

/**
 * Class CarrierTest
 * @package Magento\Ups\Model
 * @magentoDbIsolation enabled
 */
class BookmarkRepositoryTest extends TestCase
{
    /** @var  BookmarkRepository */
    private $repository;

    /** @var  SortOrderBuilder */
    private $sortOrderBuilder;

    /** @var FilterBuilder */
    private $filterBuilder;

    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;

    /**
     * @magentoDataFixture Magento/Ui/_files/bookmarks.php
     */
    public function testGetListWithMultipleFiltersAndSorting()
    {
        $filter1 = $this->filterBuilder
            ->setField('namespace')
            ->setValue('bm_namespace')
            ->create();
        $filter2 = $this->filterBuilder
            ->setField('namespace')
            ->setValue('new_namespace')
            ->create();
        $filter3 = $this->filterBuilder
            ->setField('current')
            ->setValue(1)
            ->create();
        $sortOrder = $this->sortOrderBuilder
            ->setField('title')
            ->setDirection('DESC')
            ->create();

        $this->searchCriteriaBuilder->addFilters([$filter1, $filter2]);
        $this->searchCriteriaBuilder->addFilters([$filter3]);
        $this->searchCriteriaBuilder->addSortOrder($sortOrder);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        /** @var BookmarkSearchResultsInterface $result */
        $result = $this->repository->getList($searchCriteria);
        $this->assertCount(2, $result->getItems());
        $this->assertEquals('Default View', $result->getItems()[0]->getTitle());
        $this->assertEquals('Bb', $result->getItems()[1]->getTitle());
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->repository = $objectManager->create(BookmarkRepository::class);
        $this->searchCriteriaBuilder = $objectManager->create(
            SearchCriteriaBuilder::class
        );
        $this->filterBuilder = $objectManager->get(
            FilterBuilder::class
        );
        $this->sortOrderBuilder = $objectManager->get(
            SortOrderBuilder::class
        );
    }
}
