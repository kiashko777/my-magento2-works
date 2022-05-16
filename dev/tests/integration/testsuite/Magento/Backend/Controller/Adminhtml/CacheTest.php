<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Controller\Adminhtml;

use Magento\Framework\App\Cache\Frontend\Pool;
use Magento\Framework\Cache\FrontendInterface;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @magentoAppArea Adminhtml
 */
class CacheTest extends AbstractBackendController
{
    /**
     * @magentoDataFixture Magento/Backend/controllers/_files/cache/application_cache.php
     * @magentoDataFixture Magento/Backend/controllers/_files/cache/non_application_cache.php
     */
    public function testFlushAllAction()
    {
        /** @var $cache \Magento\Framework\App\Cache */
        $cache = Bootstrap::getObjectManager()->create(
            \Magento\Framework\App\Cache::class
        );
        $this->assertNotEmpty($cache->load('APPLICATION_FIXTURE'));

        $this->dispatch('backend/admin/cache/flushAll');

        /** @var $cachePool Pool */
        $this->assertFalse($cache->load('APPLICATION_FIXTURE'));

        $cachePool = Bootstrap::getObjectManager()->create(
            Pool::class
        );
        /** @var $cacheFrontend FrontendInterface */
        foreach ($cachePool as $cacheFrontend) {
            $this->assertFalse($cacheFrontend->getBackend()->load('NON_APPLICATION_FIXTURE'));
        }
    }

    /**
     * @magentoDataFixture Magento/Backend/controllers/_files/cache/application_cache.php
     * @magentoDataFixture Magento/Backend/controllers/_files/cache/non_application_cache.php
     */
    public function testFlushSystemAction()
    {
        $this->dispatch('backend/admin/cache/flushSystem');

        /** @var $cache \Magento\Framework\App\Cache */
        $cache = Bootstrap::getObjectManager()->create(
            \Magento\Framework\App\Cache::class
        );
        /** @var $cachePool Pool */
        $this->assertFalse($cache->load('APPLICATION_FIXTURE'));

        $cachePool = Bootstrap::getObjectManager()->create(
            Pool::class
        );
        /** @var $cacheFrontend FrontendInterface */
        foreach ($cachePool as $cacheFrontend) {
            $this->assertSame(
                'non-application cache data',
                $cacheFrontend->getBackend()->load('NON_APPLICATION_FIXTURE')
            );
        }
    }

    /**
     * @dataProvider massActionsInvalidTypesDataProvider
     * @param $action
     */
    public function testMassActionsInvalidTypes($action)
    {
        $this->getRequest()->setParams(['types' => ['invalid_type_1', 'invalid_type_2', 'config']]);
        $this->dispatch('backend/admin/cache/' . $action);
        $this->assertSessionMessages(
            $this->containsEqual("These cache type(s) don&#039;t exist: invalid_type_1, invalid_type_2"),
            MessageInterface::TYPE_ERROR
        );
    }

    /**
     * @return array
     */
    public function massActionsInvalidTypesDataProvider()
    {
        return [
            'enable' => ['massEnable'],
            'disable' => ['massDisable'],
            'refresh' => ['massRefresh']
        ];
    }
}
