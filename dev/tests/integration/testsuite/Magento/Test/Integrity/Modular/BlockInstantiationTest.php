<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test\Integrity\Modular;

use Exception;
use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Backend\Block\Template;
use Magento\Customer\Model\Context;
use Magento\Customer\Model\GroupManagement;
use Magento\Framework\App\Area;
use Magento\Framework\App\AreaList;
use Magento\Framework\App\Utility\AggregateInvoker;
use Magento\Framework\App\Utility\Classes;
use Magento\Framework\Config\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Website;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractIntegrity;
use ReflectionClass;

/**
 * This test ensures that all blocks have the appropriate constructor arguments that allow
 * them to be instantiated via the objectManager.
 *
 * @magentoAppIsolation enabled
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BlockInstantiationTest extends AbstractIntegrity
{
    public function testBlockInstantiation()
    {
        $invoker = new AggregateInvoker($this);
        $invoker(
            function ($module, $class, $area) {
                $this->assertNotEmpty($module);
                $this->assertTrue(class_exists($class), "Block class: {$class}");
                Bootstrap::getObjectManager()->get(
                    ScopeInterface::class
                )->setCurrentScope(
                    $area
                );
                $context = Bootstrap::getObjectManager()->get(
                    \Magento\Framework\App\Http\Context::class
                );
                $context->setValue(Context::CONTEXT_AUTH, false, false);
                $context->setValue(
                    Context::CONTEXT_GROUP,
                    GroupManagement::NOT_LOGGED_IN_ID,
                    GroupManagement::NOT_LOGGED_IN_ID
                );
                Bootstrap::getInstance()->loadArea($area);

                try {
                    Bootstrap::getObjectManager()->create($class);
                } catch (Exception $e) {
                    throw new Exception("Unable to instantiate '{$class}'", 0, $e);
                }
            },
            $this->allBlocksDataProvider()
        );
    }

    /**
     * @return array
     */
    public function allBlocksDataProvider()
    {
        $blockClass = '';
        try {
            /** @var $website Website */
            Bootstrap::getObjectManager()->get(
                StoreManagerInterface::class
            )->getStore()->setWebsiteId(
                0
            );

            $enabledModules = $this->_getEnabledModules();
            $skipBlocks = $this->_getBlocksToSkip();
            $templateBlocks = [];
            $blockMods = Classes::collectModuleClasses('Block');
            foreach ($blockMods as $blockClass => $module) {
                if (!isset($enabledModules[$module]) || isset($skipBlocks[$blockClass])) {
                    continue;
                }
                $class = new ReflectionClass($blockClass);
                if ($class->isAbstract() || !$class->isSubclassOf(\Magento\Framework\View\Element\Template::class)) {
                    continue;
                }
                $templateBlocks = $this->_addBlock($module, $blockClass, $class, $templateBlocks);
            }
            asort($templateBlocks);
            return $templateBlocks;
        } catch (Exception $e) {
            trigger_error(
                "Corrupted data provider. Last known block instantiation attempt: '{$blockClass}'." .
                " Exception: {$e}",
                E_USER_ERROR
            );
        }
    }

    /**
     * Loads block classes, that should not be instantiated during the instantiation test
     *
     * @return array
     */
    protected function _getBlocksToSkip()
    {
        $result = [];
        foreach (glob(__DIR__ . '/_files/skip_blocks*.php') as $file) {
            $blocks = include $file;
            $result = array_merge($result, $blocks);
        }
        return array_combine($result, $result);
    }

    /**
     * @param $module
     * @param $blockClass
     * @param $class
     * @param $templateBlocks
     * @return mixed
     */
    private function _addBlock($module, $blockClass, $class, $templateBlocks)
    {
        $area = 'frontend';
        if ($module == 'Magento_Backend' || strpos(
                $blockClass,
                '\\Adminhtml\\'
            ) !== false || strpos(
                $blockClass,
                '_Backend_'
            ) !== false || $class->isSubclassOf(
                Template::class
            )
        ) {
            $area = 'Adminhtml';
        }
        Bootstrap::getObjectManager()->get(
            AreaList::class
        )->getArea(
            FrontNameResolver::AREA_CODE
        )->load(
            Area::PART_CONFIG
        );
        $templateBlocks[$module . ', ' . $blockClass . ', ' . $area] = [$module, $blockClass, $area];
        return $templateBlocks;
    }
}
