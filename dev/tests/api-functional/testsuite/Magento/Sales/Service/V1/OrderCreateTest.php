<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Service\V1;

use Magento\Catalog\Api\Data\ProductCustomOptionInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemFactory;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\Data\OrderPaymentFactory;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\AddressRepository;
use Magento\Sales\Model\Order\ItemFactory;
use Magento\Sales\Model\Order\PaymentFactory;
use Magento\Sales\Model\OrderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;
use ReflectionClass;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class OrderCreateTest extends WebapiAbstract
{
    const RESOURCE_PATH = '/V1/orders';

    const SERVICE_READ_NAME = 'salesOrderRepositoryV1';

    const SERVICE_VERSION = 'V1';

    const ORDER_INCREMENT_ID = '100000001';

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testOrderCreate()
    {
        $order = $this->prepareOrder();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => Request::HTTP_METHOD_POST,
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'save',
            ],
        ];
        $this->assertNotEmpty($this->_webApiCall($serviceInfo, ['entity' => $order]));

        /** @var Order $model */
        $model = $this->objectManager->get(Order::class);
        $model->load($order['customer_email'], 'customer_email');
        $this->assertTrue((bool)$model->getId());
        $this->assertEquals($order['base_grand_total'], $model->getBaseGrandTotal());
        $this->assertEquals($order['grand_total'], $model->getGrandTotal());
        $this->assertNotNull($model->getShippingAddress());
        $this->assertTrue((bool)$model->getShippingAddress()->getId());
        $this->assertEquals('Flat Rate - Fixed', $model->getShippingDescription());
        $shippingMethod = $model->getShippingMethod(true);
        $this->assertEquals('flatrate', $shippingMethod['carrier_code']);
        $this->assertEquals('flatrate', $shippingMethod['method']);
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function prepareOrder()
    {
        /** @var Order $orderBuilder */
        $orderFactory = $this->objectManager->get(OrderFactory::class);
        /** @var OrderItemFactory $orderItemFactory */
        $orderItemFactory = $this->objectManager->get(ItemFactory::class);
        /** @var OrderPaymentFactory $orderPaymentFactory */
        $orderPaymentFactory = $this->objectManager->get(PaymentFactory::class);
        /** @var AddressRepository $orderAddressRepository */
        $orderAddressRepository = $this->objectManager->get(AddressRepository::class);
        /** @var  StoreManagerInterface $storeManager */
        $storeManager = $this->objectManager->get(StoreManagerInterface::class);

        $order = $orderFactory->create(
            ['data' => $this->getDataStructure(OrderInterface::class)]
        );
        $orderItem = $orderItemFactory->create(
            ['data' => $this->getDataStructure(OrderItemInterface::class)]
        );
        $orderPayment = $orderPaymentFactory->create(
            ['data' => $this->getDataStructure(OrderPaymentInterface::class)]
        );

        $email = uniqid() . 'email@example.com';
        $orderItem->setSku('sku#1');
        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $orderItem->setData('parent_item', $orderItem->getData() + ['parent_item' => null]);
            $orderItem->setAdditionalData('test');
        } else {
            $orderItem->setData('parent_item', ['weight' => 1]);
        }

        $orderPayment->setCcLast4('4444');
        $orderPayment->setMethod('checkmo');
        $orderPayment->setAccountStatus('ok');
        $orderPayment->setAdditionalInformation([]);
        $order->setCustomerEmail($email);
        $order->setBaseGrandTotal(100);
        $order->setGrandTotal(100);
        $order->setShippingDescription('Flat Rate - Fixed');
        $order->setIsVirtual(0);
        $order->setStoreId($storeManager->getDefaultStoreView()->getId());
        $order->setBaseDiscountAmount(0);
        $order->setBaseShippingAmount(5);
        $order->setBaseShippingTaxAmount(0);
        $order->setBaseSubtotal(100);
        $order->setBaseTaxAmount(0);
        $order->setBaseToGlobalRate(1);
        $order->setBaseToOrderRate(1);
        $order->setDiscountAmount(0);
        $order->setShippingAmount(0);
        $order->setShippingTaxAmount(0);
        $order->setStoreToOrderRate(0);
        $order->setBaseToOrderRate(0);
        $order->setSubtotal(100);
        $order->setTaxAmount(0);
        $order->setTotalQtyOrdered(1);
        $order->setCustomerIsGuest(1);
        $order->setCustomerNoteNotify(0);
        $order->setCustomerGroupId(0);
        $order->setBaseSubtotalInclTax(100);
        $order->setWeight(1);
        $order->setBaseCurrencyCode('USD');
        $order->setShippingInclTax(5);
        $order->setBaseShippingInclTax(5);

        $this->addProductOption($orderItem);

        $order->setItems([$orderItem->getData()]);
        $order->setData('payment', $orderPayment->getData());

        $orderAddressBilling = $orderAddressRepository->create();

        $orderAddressBilling->setCity('City');
        $orderAddressBilling->setPostcode('12345');
        $orderAddressBilling->setLastname('Last Name');
        $orderAddressBilling->setFirstname('First Name');
        $orderAddressBilling->setTelephone('+00(000)-123-45-57');
        $orderAddressBilling->setStreet(['Street']);
        $orderAddressBilling->setCountryId('US');
        $orderAddressBilling->setRegion('California');
        $orderAddressBilling->setAddressType('billing');
        $orderAddressBilling->setRegionId(12);

        $orderAddressShipping = $orderAddressRepository->create();
        $orderAddressShipping->setCity('City2');
        $orderAddressShipping->setPostcode('12345');
        $orderAddressShipping->setLastname('Last Name2');
        $orderAddressShipping->setFirstname('First Name2');
        $orderAddressShipping->setTelephone('+00(000)-123-45-57');
        $orderAddressShipping->setStreet(['Street']);
        $orderAddressShipping->setCountryId('US');
        $orderAddressShipping->setRegion('California');
        $orderAddressShipping->setAddressType('shipping');
        $orderAddressShipping->setRegionId(12);

        $orderData = $order->getData();
        $orderData['billing_address'] = $orderAddressBilling->getData();
        $orderData['billing_address']['street'] = ['Street'];
        $address = $orderAddressShipping->getData();
        $address['street'] = ['Street'];
        $orderData['extension_attributes']['shipping_assignments'] =
            [
                [
                    'shipping' => [
                        'address' => $address,
                        'method' => 'flatrate_flatrate'
                    ],
                    'items' => [$orderItem->getData()],
                    'stock_id' => null,
                ]
            ];
        return $orderData;
    }

    protected function getDataStructure($className)
    {
        $refClass = new ReflectionClass($className);
        $constants = $refClass->getConstants();
        $data = array_fill_keys($constants, null);
        unset($data['custom_attributes']);
        return $data;
    }

    /**
     * @param array $orderItem
     * @return array
     */
    protected function addProductOption($orderItem)
    {
        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = $this->objectManager->create(ProductRepositoryInterface::class);
        $product = $productRepository->get('simple');
        $options = [];
        foreach ($product->getOptions() as $option) {
            $options[] = [
                'option_id' => $option->getId(),
                'option_value' => $this->getOptionRequestValue($option),
            ];
        }
        $data['extension_attributes']['custom_options'] = $options;
        $orderItem->setData('product_option', $data);
        $orderItem->setPrice(10);
        $orderItem->setBasePrice(10);
    }

    /**
     * @param ProductCustomOptionInterface $option
     * @return null|string
     */
    protected function getOptionRequestValue(ProductCustomOptionInterface $option)
    {
        $returnValue = null;
        switch ($option->getType()) {
            case 'field':
                $returnValue = 'Test value';
                break;
            case 'date_time':
                $returnValue = '2015-09-09 07:16:00';
                break;
            case 'drop_down':
                $returnValue = '3-1-select';
                break;
            case 'radio':
                $returnValue = '4-1-radio';
                break;
        }
        return $returnValue;
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }
}
