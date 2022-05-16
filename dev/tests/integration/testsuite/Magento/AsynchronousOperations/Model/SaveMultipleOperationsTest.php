<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AsynchronousOperations\Model;

use Magento\AsynchronousOperations\Api\Data\BulkSummaryInterfaceFactory;
use Magento\AsynchronousOperations\Api\Data\OperationInterface;
use Magento\AsynchronousOperations\Api\Data\OperationInterfaceFactory;
use Magento\AsynchronousOperations\Api\SaveMultipleOperationsInterface;
use Magento\Framework\EntityManager\EntityManager;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class SaveMultipleOperationsTest extends TestCase
{

    private const BULK_UUID = "bulk-uuid-multiple-0";

    /**
     * @var BulkStatus
     */
    private $bulkStatusManagement;

    /**
     * @var OperationInterfaceFactory
     */
    private $operationFactory;

    /**
     * @var SaveMultipleOperationsInterface
     */
    private $saveMultipleOperationsInterface;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var BulkSummaryInterfaceFactory
     */
    private $bulkSummaryFactory;

    /**
     * Test execute() of SaveMultipleOperations
     */
    public function testExecute()
    {
        $operation = $this->createOperation();
        $operations = [$operation, $operation, $operation];

        $bulkSummary = $this->bulkSummaryFactory->create();
        $this->entityManager->load($bulkSummary, self::BULK_UUID);
        $bulkSummary->setBulkId(self::BULK_UUID);
        $bulkSummary->setDescription("Test Bulk");
        $bulkSummary->setUserId(1);
        $bulkSummary->setUserType(1);
        $bulkSummary->setOperationCount(count($operations));
        $this->entityManager->save($bulkSummary);

        $this->saveMultipleOperationsInterface->execute($operations);
        $operationsCount = $this->bulkStatusManagement
            ->getOperationsCountByBulkIdAndStatus(self::BULK_UUID, OperationInterface::STATUS_TYPE_OPEN);
        $this->assertEquals($operationsCount, 3);
    }

    /**
     * Create Operation object and pre-fill with test data
     * @return OperationInterface
     */
    public function createOperation()
    {
        $serializedData = [
            'entity_id' => null,
            'entity_link' => '',
            'meta_information' => json_encode([
                'entity_id' => 5,
                'meta_information' => 'Test'
            ])
        ];

        $data = [
            'data' => [
                OperationInterface::BULK_ID => self::BULK_UUID,
                OperationInterface::TOPIC_NAME => "topic-4",
                OperationInterface::SERIALIZED_DATA => json_encode($serializedData),
                OperationInterface::STATUS => OperationInterface::STATUS_TYPE_OPEN,
            ],
        ];
        return $this->operationFactory->create($data);
    }

    /**
     * Set Up the test
     */
    protected function setUp(): void
    {
        $this->saveMultipleOperationsInterface = Bootstrap::getObjectManager()->create(
            SaveMultipleOperationsInterface::class
        );
        $this->operationFactory = Bootstrap::getObjectManager()->create(
            OperationInterfaceFactory::class
        );
        $this->bulkStatusManagement = Bootstrap::getObjectManager()->create(
            BulkStatus::class
        );
        $this->bulkSummaryFactory = Bootstrap::getObjectManager()->create(
            BulkSummaryInterfaceFactory::class
        );
        $this->entityManager = Bootstrap::getObjectManager()->create(
            EntityManager::class
        );
    }
}
