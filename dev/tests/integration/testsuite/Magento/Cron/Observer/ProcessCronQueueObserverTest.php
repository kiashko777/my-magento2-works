<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Cron\Observer;

use Magento\Cron\Model\ResourceModel\Schedule\Collection;
use Magento\Cron\Model\Schedule;
use Magento\Framework\App\Area;
use Magento\Framework\App\AreaList;
use Magento\Framework\App\Console\Request;
use Magento\Framework\Event\Observer;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ProcessCronQueueObserverTest extends TestCase
{
    /**
     * @var ProcessCronQueueObserver
     */
    private $_model = null;

    /**
     * @magentoConfigFixture current_store crontab/default/jobs/catalog_product_alert/schedule/cron_expr * * * * *
     */
    public function testDispatchScheduled()
    {
        $collection = Bootstrap::getObjectManager()->create(
            Collection::class
        );
        $collection->addFieldToFilter('status', Schedule::STATUS_PENDING);
        $collection->addFieldToFilter('job_code', 'catalog_product_alert');
        $this->assertGreaterThan(0, $collection->count(), 'Cron has failed to schedule tasks for itself for future.');
    }

    public function testDispatchNoFailed()
    {
        $collection = Bootstrap::getObjectManager()->create(
            Collection::class
        );
        $collection->addFieldToFilter('status', Schedule::STATUS_ERROR);
        foreach ($collection as $item) {
            $this->fail($item->getMessages());
        }
    }

    protected function setUp(): void
    {
        Bootstrap::getObjectManager()->get(AreaList::class)
            ->getArea('crontab')
            ->load(Area::PART_CONFIG);
        $request = Bootstrap::getObjectManager()->create(Request::class);
        $request->setParams(['group' => 'default', 'standaloneProcessStarted' => '0']);
        $this->_model = Bootstrap::getObjectManager()
            ->create(ProcessCronQueueObserver::class, ['request' => $request]);
        $this->_model->execute(new Observer());
    }
}
