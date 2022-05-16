<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Service\V1;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Sales\Model\Order\Payment\Transaction\Repository;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Class TransactionReadTest
 */
class TransactionTest extends WebapiAbstract
{
    /**
     * Service read name
     */
    const SERVICE_READ_NAME = 'salesTransactionRepositoryV1';

    /**
     * Resource path for REST
     */
    const RESOURCE_PATH = '/V1/transactions';

    /**
     * Service version
     */
    const SERVICE_VERSION = 'V1';

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Tests list of order transactions
     *
     * @magentoApiDataFixture Magento/Sales/_files/transactions_detailed.php
     */
    public function testTransactionGet()
    {
        /** @var Order $order */
        $order = $this->objectManager->create(Order::class);
        /**
         * @var $transactionRepository Repository
         */
        $transactionRepository = Repository::class;
        $transactionRepository = $this->objectManager->create($transactionRepository);
        $order->loadByIncrementId('100000006');

        /** @var Payment $payment */
        $payment = $order->getPayment();
        /** @var Transaction $transaction */
        $transaction = $transactionRepository->getByTransactionId('trx_auth', $payment->getId(), $order->getId());

        $childTransactions = $transaction->getChildTransactions();
        $childTransaction = reset($childTransactions);

        $expectedData = $this->getPreparedTransactionData($transaction);
        $childTransactionData = $this->getPreparedTransactionData($childTransaction);
        $expectedData['child_transactions'][] = $childTransactionData;

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $transaction->getId(),
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'get',
            ],
        ];
        $result = $this->_webApiCall($serviceInfo, ['id' => $transaction->getId()]);
        ksort($expectedData);
        ksort($result);
        $this->assertEquals($expectedData, $result);
    }

    /**
     * @param Transaction $transaction
     * @return array
     */
    private function getPreparedTransactionData(Transaction $transaction)
    {
        $additionalInfo = [];
        foreach ($transaction->getAdditionalInformation() as $value) {
            $additionalInfo[] = $value;
        }

        $expectedData = ['transaction_id' => (int)$transaction->getId()];

        if ($transaction->getParentId() !== null) {
            $expectedData['parent_id'] = (int)$transaction->getParentId();
        }

        $expectedData = array_merge(
            $expectedData,
            [
                'order_id' => (int)$transaction->getOrderId(),
                'payment_id' => (int)$transaction->getPaymentId(),
                'txn_id' => $transaction->getTxnId(),
                'parent_txn_id' => ($transaction->getParentTxnId() ? (string)$transaction->getParentTxnId() : null),
                'txn_type' => $transaction->getTxnType(),
                'is_closed' => (int)$transaction->getIsClosed(),
                'additional_information' => ['data'],
                'created_at' => $transaction->getCreatedAt(),
                'child_transactions' => [],
            ]
        );

        return $expectedData;
    }

    /**
     * Tests list of order transactions
     * @magentoApiDataFixture Magento/Sales/_files/transactions_list.php
     */
    public function testTransactionList()
    {
        /** @var Order $order */
        $order = $this->objectManager->create(Order::class);
        /**
         * @var $transactionRepository Repository
         */
        $transactionRepository = Repository::class;
        $transactionRepository = $this->objectManager->create($transactionRepository);
        $order->loadByIncrementId('100000006');

        /** @var Payment $payment */
        $payment = $order->getPayment();
        /** @var Transaction $transaction */
        $transaction = $transactionRepository->getByTransactionId('trx_auth', $payment->getId(), $order->getId());

        $childTransactions = $transaction->getChildTransactions();

        $childTransaction = reset($childTransactions);

        /** @var $searchCriteriaBuilder  SearchCriteriaBuilder */
        $searchCriteriaBuilder = $this->objectManager->create(
            SearchCriteriaBuilder::class
        );
        /** @var $filterBuilder  FilterBuilder */
        $filterBuilder = $this->objectManager->create(
            FilterBuilder::class
        );
        /** @var SortOrderBuilder $sortOrderBuilder */
        $sortOrderBuilder = $this->objectManager->create(
            SortOrderBuilder::class
        );
        $filter1 = $filterBuilder->setField('txn_id')
            ->setValue('%trx_auth%')
            ->setConditionType('like')
            ->create();
        $filter2 = $filterBuilder->setField('txn_id')
            ->setValue('trx_capture')
            ->setConditionType('eq')
            ->create();
        $filter3 = $filterBuilder->setField('parent_txn_id')
            ->setValue(null)
            ->setConditionType('null')
            ->create();
        $filter4 = $filterBuilder->setField('is_closed')
            ->setValue(0)
            ->setConditionType('eq')
            ->create();
        $sortOrder = $sortOrderBuilder->setField('parent_id')
            ->setDirection('ASC')
            ->create();

        $searchCriteriaBuilder->addFilters([$filter1, $filter2]);
        $searchCriteriaBuilder->addFilters([$filter3, $filter4]);
        $searchCriteriaBuilder->addSortOrder($sortOrder);
        $searchCriteriaBuilder->setPageSize(20);
        $searchData = $searchCriteriaBuilder->create()->__toArray();

        $requestData = ['searchCriteria' => $searchData];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '?' . http_build_query($requestData),
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'getList',
            ],
        ];
        $result = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertArrayHasKey('items', $result);

        $transactionData = $this->getPreparedTransactionData($transaction);
        $childTransactionData = $this->getPreparedTransactionData($childTransaction);
        $transactionData['child_transactions'][] = $childTransactionData;
        $expectedData = [$transactionData, $childTransactionData];
        $this->assertEquals($expectedData, $result['items']);
        $this->assertArrayHasKey('search_criteria', $result);
        $this->assertEquals($searchData, $result['search_criteria']);
    }

    /**
     * @return array
     * @deprecated
     */
    public function filtersDataProvider()
    {
        /** @var $filterBuilder  FilterBuilder */
        $filterBuilder = Bootstrap::getObjectManager()->create(
            FilterBuilder::class
        );

        return [
            [
                [
                    $filterBuilder->setField('created_at')->setValue('2020-12-12 00:00:00')
                        ->setConditionType('lteq')->create(),
                ],
            ]
        ];
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }
}
