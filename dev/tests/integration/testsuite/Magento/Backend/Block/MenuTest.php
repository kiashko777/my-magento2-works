<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Block;

use Magento\Backend\Model\Auth;
use Magento\Backend\Model\Menu\Config;
use Magento\Backend\Model\Menu\Config\Reader;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\Arguments\ValidationState;
use Magento\Framework\App\State;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\TestFramework\Bootstrap;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use SimpleXMLElement;

/**
 * Test class for \Magento\Backend\Block\Menu
 * @magentoAppArea Adminhtml
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MenuTest extends TestCase
{
    /**
     * @var Menu $blockMenu
     */
    protected $blockMenu;

    /** @var \Magento\Framework\App\Cache\Type\Config $configCacheType */
    protected $configCacheType;

    /**
     * @var array
     */
    protected $backupRegistrar;

    /**
     * @var Config
     */
    private $menuConfig;

    /**
     * Verify that Admin Navigation Menu elements have correct titles & are located on correct levels
     */
    public function testRenderNavigation()
    {
        $menuHtml = $this->blockMenu->renderNavigation($this->menuConfig->getMenu());
        $menu = new SimpleXMLElement($menuHtml);

        $item = $menu->xpath('/ul/li/a/span')[0];
        $this->assertEquals('System', (string)$item, '"System" item is absent or located on wrong menu level.');

        $item = $menu->xpath('/ul//ul/li/strong/span')[0];
        $this->assertEquals('Report', (string)$item, '"Report" item is absent or located on wrong menu level.');

        $liTitles = [
            'Private Sales',
            'Invite',
            'Invited Customers',
        ];
        foreach ($menu->xpath('/ul//ul//ul/li/a/span') as $sortOrder => $item) {
            $this->assertEquals(
                $liTitles[$sortOrder],
                (string)$item,
                '"' . $liTitles[$sortOrder] . '" item is absent or located on wrong menu level.'
            );
        }
    }

    protected function setUp(): void
    {
        $this->configCacheType = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            \Magento\Framework\App\Cache\Type\Config::class
        );
        $this->configCacheType->save('', Config::CACHE_MENU_OBJECT);

        $reflection = new ReflectionClass(ComponentRegistrar::class);
        $paths = $reflection->getProperty('paths');
        $paths->setAccessible(true);
        $this->backupRegistrar = $paths->getValue();
        $paths->setAccessible(false);

        $this->menuConfig = $this->prepareMenuConfig();

        $this->blockMenu = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            Menu::class,
            ['menuConfig' => $this->menuConfig]
        );
    }

    /**
     * @return Config
     */
    protected function prepareMenuConfig()
    {
        $this->loginAdminUser();

        $componentRegistrar = new ComponentRegistrar();
        $libraryPath = $componentRegistrar->getPath(ComponentRegistrar::LIBRARY, 'magento/framework');

        $reflection = new ReflectionClass(ComponentRegistrar::class);
        $paths = $reflection->getProperty('paths');
        $paths->setAccessible(true);

        $paths->setValue(
            [
                ComponentRegistrar::MODULE => [],
                ComponentRegistrar::THEME => [],
                ComponentRegistrar::LANGUAGE => [],
                ComponentRegistrar::LIBRARY => []
            ]
        );
        $paths->setAccessible(false);

        ComponentRegistrar::register(ComponentRegistrar::LIBRARY, 'magento/framework', $libraryPath);

        ComponentRegistrar::register(
            ComponentRegistrar::MODULE,
            'Magento_Backend',
            __DIR__ . '/_files/menu/Magento/Backend'
        );

        /* @var $validationState ValidationState */
        $validationState = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            ValidationState::class,
            ['appMode' => State::MODE_DEFAULT]
        );

        /* @var $configReader Reader */
        $configReader = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            Reader::class,
            ['validationState' => $validationState]
        );

        return \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            Config::class,
            [
                'configReader' => $configReader,
                'configCacheType' => $this->configCacheType
            ]
        );
    }

    /**
     * @return void
     */
    protected function loginAdminUser()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get(UrlInterface::class)
            ->turnOffSecretKey();

        $auth = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(Auth::class);
        $auth->login(Bootstrap::ADMIN_NAME, Bootstrap::ADMIN_PASSWORD);
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        $this->configCacheType->save('', Config::CACHE_MENU_OBJECT);
        $reflection = new ReflectionClass(ComponentRegistrar::class);
        $paths = $reflection->getProperty('paths');
        $paths->setAccessible(true);
        $paths->setValue($this->backupRegistrar);
        $paths->setAccessible(false);
    }
}
