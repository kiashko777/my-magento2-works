<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Cms\Model\Wysiwyg;

use Magento\Framework\DataObject;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestModuleWysiwygConfig\Model\Config as TestModuleWysiwygConfig;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class ConfigTest extends TestCase
{
    /**
     * @var Config
     */
    private $model;

    /**
     * Tests that config returns valid config array in it
     *
     * @return void
     */
    public function testGetConfig()
    {
        $config = $this->model->getConfig();
        $this->assertInstanceOf(DataObject::class, $config);
    }

    /**
     * Tests that config returns right urls going to the published library path
     *
     * @return void
     */
    public function testGetConfigCssUrls()
    {
        $config = $this->model->getConfig();
        $publicPathPattern = 'http://localhost/static/%s/Adminhtml/Magento/backend/en_US/%s';
        $tinyMce4Config = $config->getData('tinymce4');
        $contentCss = $tinyMce4Config['content_css'];
        if (is_array($contentCss)) {
            foreach ($contentCss as $url) {
                $this->assertStringMatchesFormat($publicPathPattern, $url);
            }
        } else {
            $this->assertStringMatchesFormat($publicPathPattern, $contentCss);
        }
    }

    /**
     * Test enabled module is able to modify WYSIWYG config
     *
     * @return void
     *
     * @magentoConfigFixture default/cms/wysiwyg/editor Magento_TestModuleWysiwygConfig/wysiwyg/tinymce4TestAdapter
     */
    public function testTestModuleEnabledModuleIsAbleToModifyConfig()
    {
        $objectManager = Bootstrap::getObjectManager();
        $compositeConfigProvider = $objectManager->create(CompositeConfigProvider::class);
        $model = $objectManager->create(
            Config::class,
            ['configProvider' => $compositeConfigProvider]
        );
        $config = $model->getConfig();
        // @phpstan-ignore-next-line
        $this->assertEquals(TestModuleWysiwygConfig::CONFIG_HEIGHT, $config['height']);
        // @phpstan-ignore-next-line
        $this->assertEquals(TestModuleWysiwygConfig::CONFIG_CONTENT_CSS, $config['content_css']);
        $this->assertArrayHasKey('tinymce4', $config);
        $this->assertArrayHasKey('toolbar', $config['tinymce4']);
        $this->assertStringNotContainsString(
            'charmap',
            $config['tinymce4']['toolbar'],
            'Failed to address that the custom test module removes "charmap" button from the toolbar'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->model = $objectManager->create(Config::class);
    }
}
