<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Widget\Model\ResourceModel\Layout;

use Magento\Framework\App\Cache;
use Magento\Framework\App\Cache\Type\Layout;
use Magento\Framework\View\Design\ThemeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class UpdateTest extends TestCase
{
    /**
     * @var Update
     */
    protected $_resourceModel;

    /**
     * @magentoDataFixture Magento/Widget/_files/layout_update.php
     */
    public function testFetchUpdatesByHandle()
    {
        /** @var $theme ThemeInterface */
        $theme = Bootstrap::getObjectManager()->create(
            ThemeInterface::class
        );
        $theme->load('Test Theme', 'theme_title');
        $result = $this->_resourceModel->fetchUpdatesByHandle(
            'test_handle',
            $theme,
            Bootstrap::getObjectManager()->get(
                StoreManagerInterface::class
            )->getStore()
        );
        $this->assertEquals('not_temporary', $result);
    }

    /**
     * @magentoDataFixture Magento/Backend/controllers/_files/cache/application_cache.php
     * @magentoDataFixture Magento/Widget/_files/layout_cache.php
     */
    public function testSaveAfterClearCache()
    {
        /** @var $appCache Cache */
        $appCache = Bootstrap::getObjectManager()->get(
            Cache::class
        );
        /** @var Layout $layoutCache */
        $layoutCache = Bootstrap::getObjectManager()->get(
            Layout::class
        );

        $this->assertNotEmpty($appCache->load('APPLICATION_FIXTURE'));
        $this->assertNotEmpty($layoutCache->load('LAYOUT_CACHE_FIXTURE'));

        /** @var $layoutUpdate \Magento\Widget\Model\Layout\Update */
        $layoutUpdate = Bootstrap::getObjectManager()->create(
            \Magento\Widget\Model\Layout\Update::class
        );
        $layoutUpdate->setHasDataChanges(true);
        $this->_resourceModel->save($layoutUpdate);

        $this->assertNotEmpty($appCache->load('APPLICATION_FIXTURE'), 'Non-layout cache must be kept');
        $this->assertFalse($layoutCache->load('LAYOUT_CACHE_FIXTURE'), 'Layout cache must be erased');
    }

    protected function setUp(): void
    {
        $this->_resourceModel = Bootstrap::getObjectManager()->create(
            Update::class
        );
    }
}
