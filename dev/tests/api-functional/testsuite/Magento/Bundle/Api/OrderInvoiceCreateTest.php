<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Bundle\Api;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * API test for creation of Invoice for order with bundle product.
 */
class OrderInvoiceCreateTest extends WebapiAbstract
{
    const SERVICE_READ_NAME = 'salesInvoiceOrderV1';
    const SERVICE_VERSION = 'V1';

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var InvoiceRepositoryInterface
     */
    private $invoiceRepository;

    /**
     * Test create a partial invoice for order with bundle and Simple products.
     *
     * @return void
     * @magentoApiDataFixture Magento/Bundle/_files/order_items_simple_and_bundle.php
     */
    public function testInvoiceWithSimpleAndBundleCreate()
    {
        /** @var OrderInterface $existingOrder */
        $existingOrder = $this->objectManager->create(OrderInterface::class)
            ->loadByIncrementId('100000001');

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/order/' . $existingOrder->getId() . '/invoice',
                'httpMethod' => Request::HTTP_METHOD_POST,
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'execute',
            ],
        ];

        $requestData = [
            'orderId' => $existingOrder->getId(),
            'items' => [],
            'comment' => [
                'comment' => 'Test Comment',
                'is_visible_on_front' => 1,
            ],
        ];
        $grantTotal = 0;
        foreach ($existingOrder->getAllItems() as $item) {
            $requestData['items'] = [];
            $requestData['items'][] = [
                'order_item_id' => $item->getItemId(),
                'qty' => $item->getQtyOrdered(),
            ];
            $result = $this->_webApiCall($serviceInfo, $requestData);
            $this->assertNotEmpty($result);
            try {
                $invoice = $this->invoiceRepository->get($result);
                $grantTotal += $invoice->getGrandTotal();
            } catch (NoSuchEntityException $e) {
                $this->fail('Failed asserting that Invoice was created');
            }
        }
        $this->assertNotEquals(
            $existingOrder->getGrandTotal(),
            $grantTotal,
            'Failed asserting that invoice is correct.'
        );
    }

    /**
     * Test create invoice with Bundle product.
     *
     * @return void
     * @magentoApiDataFixture Magento/Bundle/_files/order_item_with_bundle_and_options.php
     */
    public function testInvoiceWithBundleCreate()
    {
        /** @var OrderInterface $existingOrder */
        $existingOrder = $this->objectManager->create(OrderInterface::class)
            ->loadByIncrementId('100000001');

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/order/' . $existingOrder->getId() . '/invoice',
                'httpMethod' => Request::HTTP_METHOD_POST,
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'execute',
            ],
        ];

        $requestData = [
            'orderId' => $existingOrder->getId(),
            'items' => [],
            'comment' => [
                'comment' => 'Test Comment',
                'is_visible_on_front' => 1,
            ],
        ];

        /** @var OrderItemInterface $item */
        foreach ($existingOrder->getAllItems() as $item) {
            $requestData['items'][] = [
                'order_item_id' => $item->getItemId(),
                'qty' => $item->getQtyOrdered(),
            ];
        }
        $result = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertNotEmpty($result);
        $invoice = $this->invoiceRepository->get($result);
        $this->assertNotEquals(
            $existingOrder->getGrandTotal(),
            $invoice->getGrandTotal(),
            'Failed asserting that invoice is correct.'
        );
    }

    /**
     * Set up.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->invoiceRepository = $this->objectManager->get(
            InvoiceRepositoryInterface::class
        );
    }
}
