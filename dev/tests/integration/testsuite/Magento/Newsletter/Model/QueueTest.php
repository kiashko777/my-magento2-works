<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Newsletter\Model;

use Magento\Framework\App\Area;
use Magento\Framework\App\AreaList;
use Magento\Framework\App\Config\MutableScopeConfigInterface;
use Magento\Framework\App\State;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\TransportInterface;
use Magento\Newsletter\Model\Queue\TransportBuilder;
use Magento\Newsletter\Model\Template\Filter;
use Magento\Store\Model\ScopeInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

class QueueTest extends TestCase
{
    /**
     * @magentoDataFixture Magento/Newsletter/_files/queue.php
     * @magentoAppIsolation enabled
     */
    public function testSendPerSubscriber()
    {
        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();

        /** @var MutableScopeConfigInterface $mutableConfig */
        $mutableConfig = $objectManager->get(MutableScopeConfigInterface::class);
        $mutableConfig->setValue('general/locale/code', 'de_DE', ScopeInterface::SCOPE_STORE, 'fixturestore');

        $objectManager->get(
            State::class
        )->setAreaCode(Area::AREA_FRONTEND);
        $area = $objectManager->get(AreaList::class)
            ->getArea(Area::AREA_FRONTEND);
        $area->load();

        /** @var $filter Filter */
        $filter = $objectManager->get(Filter::class);

        $transport = $this->getMockBuilder(TransportInterface::class)
            ->setMethods(['sendMessage'])
            ->getMockForAbstractClass();
        $transport->expects($this->exactly(2))->method('sendMessage')->willReturnSelf();

        $builder = $this->createPartialMock(
            TransportBuilder::class,
            ['getTransport', 'setFrom', 'addTo']
        );
        $builder->expects($this->exactly(2))->method('getTransport')->willReturn($transport);
        $builder->expects($this->exactly(2))->method('setFrom')->willReturnSelf();
        $builder->expects($this->exactly(2))->method('addTo')->willReturnSelf();

        /** @var $queue Queue */
        $queue = $objectManager->create(
            Queue::class,
            ['filter' => $filter, 'transportBuilder' => $builder]
        );
        $queue->load('Subject', 'newsletter_subject');
        // fixture
        $queue->sendPerSubscriber();
    }

    /**
     * @magentoDataFixture Magento/Newsletter/_files/queue.php
     * @magentoAppIsolation enabled
     */
    public function testSendPerSubscriberProblem()
    {
        // md5 used here only for random string generation for test purposes. No cryptographic use.
        // phpcs:ignore Magento2.Security.InsecureFunction
        $errorMsg = md5(microtime());

        Bootstrap::getInstance()
            ->loadArea(Area::AREA_FRONTEND);

        $objectManager = Bootstrap::getObjectManager();

        $transport = $this->getMockBuilder(TransportInterface::class)
            ->setMethods(['sendMessage'])
            ->getMockForAbstractClass();
        $transport->expects($this->any())
            ->method('sendMessage')
            ->willThrowException(new MailException(__($errorMsg)));

        $builder = $this->createPartialMock(
            TransportBuilder::class,
            ['getTransport', 'setFrom', 'addTo', 'setTemplateOptions', 'setTemplateVars']
        );
        $builder->expects($this->any())->method('getTransport')->willReturn($transport);
        $builder->expects($this->any())->method('setTemplateOptions')->willReturnSelf();
        $builder->expects($this->any())->method('setTemplateVars')->willReturnSelf();
        $builder->expects($this->any())->method('setFrom')->willReturnSelf();
        $builder->expects($this->any())->method('addTo')->willReturnSelf();

        /** @var $queue Queue */
        $queue = $objectManager->create(Queue::class, ['transportBuilder' => $builder]);
        $queue->load('Subject', 'newsletter_subject');
        // fixture

        $problem = $objectManager->create(Problem::class);
        $problem->load($queue->getId(), 'queue_id');
        $this->assertEmpty($problem->getId());

        $queue->sendPerSubscriber();

        $problem->load($queue->getId(), 'queue_id');
        $this->assertNotEmpty($problem->getId());
        $this->assertEquals($errorMsg, $problem->getProblemErrorText());
    }
}
