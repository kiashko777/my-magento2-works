<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Model\Order\Payment;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Sales\Api\Data\OrderPaymentSearchResultInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Class RepositoryTest
 * @package Magento\Sales\Model\Order\Payment\
 * @magentoDbIsolation enabled
 */
class RepositoryTest extends TestCase
{
    /** @var Repository */
    protected $repository;

    /** @var  SortOrderBuilder */
    private $sortOrderBuilder;

    /** @var FilterBuilder */
    private $filterBuilder;

    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;

    /**
     * @magentoDataFixture Magento/Sales/_files/order_payment_list.php
     */
    public function testGetListWithMultipleFiltersAndSorting()
    {
        $filter1 = $this->filterBuilder
            ->setField('cc_ss_start_year')
            ->setValue('2014')
            ->create();
        $filter2 = $this->filterBuilder
            ->setField('cc_exp_month')
            ->setValue('09')
            ->create();
        $filter3 = $this->filterBuilder
            ->setField('method')
            ->setValue('checkmo')
            ->create();
        $sortOrder = $this->sortOrderBuilder
            ->setField('cc_exp_month')
            ->setDirection('DESC')
            ->create();

        $this->searchCriteriaBuilder->addFilters([$filter1, $filter2]);
        $this->searchCriteriaBuilder->addFilters([$filter3]);
        $this->searchCriteriaBuilder->addSortOrder($sortOrder);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        /** @var OrderPaymentSearchResultInterface $result */
        $result = $this->repository->getList($searchCriteria);
        $items = $result->getItems();
        $this->assertCount(2, $items);
        $this->assertEquals('456', array_shift($items)->getCcLast4());
        $this->assertEquals('123', array_shift($items)->getCcLast4());
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->repository = $objectManager->create(Repository::class);
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
