<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Sales\Controller\Adminhtml\Order;

use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Message\MessageInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\OrderRepository;
use Magento\TestFramework\Mail\Template\TransportBuilderMock;
use Magento\TestFramework\TestCase\AbstractBackendController;
use PHPUnit\Framework\Constraint\StringContains;

/**
 * Class verifies order send email functionality.
 *
 * @magentoDbIsolation enabled
 * @magentoAppArea Adminhtml
 * @magentoDataFixture Magento/Sales/_files/order.php
 */
class EmailTest extends AbstractBackendController
{
    /**
     * @var string
     */
    protected $resource = 'Magento_Sales::email';
    /**
     * @var string
     */
    protected $uri = 'backend/sales/order/email';
    /**
     * @var OrderRepository
     */
    private $orderRepository;
    /**
     * @var TransportBuilderMock
     */
    private $transportBuilder;

    /**
     * @return void
     */
    public function testSendOrderEmail(): void
    {
        $order = $this->prepareRequest();
        $this->dispatch('backend/sales/order/email');

        $this->assertSessionMessages(
            $this->equalTo([(string)__('You sent the order email.')]),
            MessageInterface::TYPE_SUCCESS
        );

        $redirectUrl = 'sales/order/view/order_id/' . $order->getEntityId();
        $this->assertRedirect($this->stringContains($redirectUrl));

        $message = $this->transportBuilder->getSentMessage();
        $subject = __('Your %1 order confirmation', $order->getStore()->getFrontendName())->render();
        $assert = $this->logicalAnd(
            new StringContains($order->getBillingAddress()->getName()),
            new StringContains(
                'Thank you for your order from ' . $order->getStore()->getFrontendName()
            ),
            new StringContains(
                "Your Order <span class=\"no-link\">#{$order->getIncrementId()}</span>"
            )
        );

        $this->assertEquals($message->getSubject(), $subject);
        $this->assertThat($message->getBody()->getParts()[0]->getRawContent(), $assert);
    }

    /**
     * @return OrderInterface|null
     */
    private function prepareRequest()
    {
        $order = $this->getOrder('100000001');
        $this->getRequest()->setParams(['order_id' => $order->getEntityId()]);

        return $order;
    }

    /**
     * @param string $incrementalId
     * @return OrderInterface|null
     */
    private function getOrder(string $incrementalId)
    {
        /** @var SearchCriteria $searchCriteria */
        $searchCriteria = $this->_objectManager->create(SearchCriteriaBuilder::class)
            ->addFilter(OrderInterface::INCREMENT_ID, $incrementalId)
            ->create();

        $orders = $this->orderRepository->getList($searchCriteria)->getItems();
        /** @var OrderInterface|null $order */
        $order = reset($orders);

        return $order;
    }

    /**
     * @inheritdoc
     */
    public function testAclHasAccess()
    {
        $this->prepareRequest();

        parent::testAclHasAccess();
    }

    /**
     * @inheritdoc
     */
    public function testAclNoAccess()
    {
        $this->prepareRequest();

        parent::testAclNoAccess();
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->orderRepository = $this->_objectManager->get(OrderRepository::class);
        $this->transportBuilder = $this->_objectManager->get(TransportBuilderMock::class);
    }
}
