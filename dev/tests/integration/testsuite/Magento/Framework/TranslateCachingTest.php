<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework;

use Magento\Framework\App\Area;
use Magento\Framework\Phrase\RendererInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Translation\Model\ResourceModel\StringUtils;
use PHPUnit\Framework\TestCase;

/**
 * Class TranslateCachingTest
 * @package Magento\Framework
 * @magentoAppIsolation enabled
 */
class TranslateCachingTest extends TestCase
{
    /**
     * @var RendererInterface
     */
    protected $renderer;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @magentoDataFixture Magento/Translation/_files/db_translate.php
     */
    public function testLoadDataCaching()
    {
        /** @var Translate $model */
        $model = $this->objectManager->get(Translate::class);

        $model->loadData(Area::AREA_FRONTEND, true); // this is supposed to cache the fixture
        $this->assertEquals('Fixture Db Translation', new Phrase('Fixture String'));

        /** @var StringUtils $translateString */
        $translateString = $this->objectManager->create(StringUtils::class);
        $translateString->saveTranslate('Fixture String', 'New Db Translation');

        $this->assertEquals(
            'Fixture Db Translation',
            new Phrase('Fixture String'),
            'Translation is expected to be cached'
        );

        $model->loadData(Area::AREA_FRONTEND, true);
        $this->assertEquals(
            'New Db Translation',
            new Phrase('Fixture String'),
            'Forced load should not use cache'
        );
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->renderer = Phrase::getRenderer();
        Phrase::setRenderer($this->objectManager->get(RendererInterface::class));
    }

    protected function tearDown(): void
    {
        Phrase::setRenderer($this->renderer);

        /** @var \Magento\Framework\App\Cache\Type\Translate $cache */
        $cache = $this->objectManager->get(\Magento\Framework\App\Cache\Type\Translate::class);
        $cache->clean();
    }
}
