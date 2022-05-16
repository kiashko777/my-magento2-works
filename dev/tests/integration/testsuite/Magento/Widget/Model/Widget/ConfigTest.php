<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Widget\Model\Widget;

use Magento\Framework\DataObject;
use Magento\Framework\View\DesignInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class ConfigTest extends TestCase
{
    /**
     * @var Config
     */
    protected $_model;

    /**
     * App isolation is enabled, because we change current area and design
     *
     * @magentoAppIsolation enabled
     */
    public function testGetPluginSettings()
    {
        Bootstrap::getObjectManager()->get(
            DesignInterface::class
        )->setDesignTheme(
            'Magento/backend'
        );

        $config = new DataObject();
        $settings = $this->_model->getPluginSettings($config);

        $this->assertArrayHasKey('plugins', $settings);
        $plugins = array_shift($settings['plugins']);
        $this->assertArrayHasKey('options', $plugins);
        $this->assertArrayHasKey('window_url', $plugins['options']);
        $this->assertArrayHasKey('placeholders', $plugins['options']);

        $jsFilename = $plugins['src'];
        $this->assertStringMatchesFormat(
            'http://localhost/static/%s/Adminhtml/Magento/backend/en_US/%s/editor_plugin.js',
            $jsFilename
        );

        $this->assertIsArray($plugins['options']['placeholders']);

        $this->assertStringStartsWith(
            'http://localhost/index.php/backend/admin/widget/index/key',
            $plugins['options']['window_url']
        );
    }

    public function testGetWidgetWindowUrl()
    {
        $config = new DataObject(['widget_filters' => ['is_email_compatible' => 1]]);

        $url = $this->_model->getWidgetWindowUrl($config);

        $this->assertStringStartsWith('http://localhost/index.php/backend/admin/widget/index/skip_widgets', $url);
    }

    protected function setUp(): void
    {
        $this->_model = Bootstrap::getObjectManager()->create(
            Config::class
        );
    }
}
