<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Security\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Class PasswordResetRequestEventTest
 * @package Magento\Security\Model
 */
class PasswordResetRequestEventTest extends TestCase
{
    /**
     * @var AbstractModel
     */
    protected $model;

    /**
     * @var PasswordResetRequestEvent
     */
    protected $resourceModel;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Checking that test data is saving to database
     *
     * @magentoDbIsolation enabled
     */
    public function testIsModelSavingDataToDatabase()
    {
        $modelId = $this->saveTestData();
        $newModel = $this->model->load($modelId);
        $testData = $this->getTestData();
        $newModelData = [];
        foreach (array_keys($testData) as $key) {
            $newModelData[$key] = $newModel->getData($key);
        }
        $this->assertEquals($testData, $newModelData);
    }

    /**
     * Saving test data to database
     * @return mixed
     */
    protected function saveTestData()
    {
        foreach ($this->getTestData() as $key => $value) {
            $this->model->setData($key, $value);
        }
        $this->model->save();
        return $this->model->getId();
    }

    /**
     * Test data
     * @return array
     */
    public function getTestData()
    {
        return [
            'request_type' => \Magento\Security\Model\PasswordResetRequestEvent::ADMIN_PASSWORD_RESET_REQUEST,
            'account_reference' => 'test27.dev@gmail.com',
            'created_at' => '2016-01-20 13:00:13',
            'ip' => '3232249856'
        ];
    }

    /**
     * @magentoDataFixture Magento/Security/_files/password_reset_request_events.php
     */
    public function testDeleteRecordsOlderThen()
    {
        /** @var \Magento\Security\Model\PasswordResetRequestEvent $passwordResetRequestEvent */
        $countBefore = $this->model->getCollection()->count();
        $this->resourceModel->deleteRecordsOlderThen(strtotime('2016-01-20 12:00:00'));
        $countAfter = $this->model->getCollection()->count();
        $this->assertLessThan($countBefore, $countAfter);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->objectManager = Bootstrap::getObjectManager();
        $this->model = $this->objectManager->create(\Magento\Security\Model\PasswordResetRequestEvent::class);
        $this->resourceModel = $this->model->getResource();
    }

    protected function tearDown(): void
    {
        $this->objectManager = null;
        $this->resourceModel = null;
        parent::tearDown();
    }
}
