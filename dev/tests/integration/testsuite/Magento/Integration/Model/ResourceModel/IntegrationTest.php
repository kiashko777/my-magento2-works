<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Integration\Model\ResourceModel;

use Magento\Integration\Model\Oauth\Consumer;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Integration test for \Magento\Integration\Model\ResourceModel\Integration
 */
class IntegrationTest extends TestCase
{
    /**
     * @var \Magento\Integration\Model\Integration
     */
    protected $integration;

    /**
     * @var Consumer
     */
    protected $consumer;

    public function testLoadActiveIntegrationByConsumerId()
    {
        $integration = $this->integration->getResource()->selectActiveIntegrationByConsumerId($this->consumer->getId());
        $this->assertEquals($this->integration->getId(), $integration['integration_id']);
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->consumer = $objectManager->create(Consumer::class);
        $this->consumer->setData(
            [
                // md5() here just to generate unique string
                // phpcs:disable Magento2.Security.InsecureFunction
                'key' => md5(uniqid()),
                'secret' => md5(uniqid()),
                // phpcs:enable
                'callback_url' => 'http://example.com/callback',
                'rejected_callback_url' => 'http://example.com/rejectedCallback'
            ]
        )->save();
        $this->integration = $objectManager->create(\Magento\Integration\Model\Integration::class);
        $this->integration->setName('Test Integration')
            ->setConsumerId($this->consumer->getId())
            ->setStatus(\Magento\Integration\Model\Integration::STATUS_ACTIVE)
            ->save();
    }
}
