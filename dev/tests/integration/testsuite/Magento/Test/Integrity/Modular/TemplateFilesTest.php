<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test\Integrity\Modular;

use Exception;
use Magento\Backend\Block\Template;
use Magento\Customer\Model\Context;
use Magento\Customer\Model\GroupManagement;
use Magento\Framework\App\Area;
use Magento\Framework\App\AreaList;
use Magento\Framework\App\State;
use Magento\Framework\App\Utility\AggregateInvoker;
use Magento\Framework\App\Utility\Classes;
use Magento\Framework\Config\ScopeInterface;
use Magento\Framework\View\Design\ThemeInterface;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\FileSystem;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Website;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractIntegrity;
use ReflectionClass;

/**
 * @magentoAppIsolation enabled
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TemplateFilesTest extends AbstractIntegrity
{
    public function testAllTemplates()
    {
        $invoker = new AggregateInvoker($this);
        $invoker(
            function ($module, $template, $class, $area) {
                Bootstrap::getObjectManager()->get(
                    DesignInterface::class
                )->setDefaultDesignTheme();
                // intentionally to make sure the module files will be requested
                $params = [
                    'area' => $area,
                    'themeModel' => Bootstrap::getObjectManager()->create(
                        ThemeInterface::class
                    ),
                    'module' => $module,
                ];
                $file = Bootstrap::getObjectmanager()->get(
                    FileSystem::class
                )->getTemplateFileName(
                    $template,
                    $params
                );
                $this->assertIsString($file, "Block class: {$class} {$template}");
                $this->assertFileExists($file, "Block class: {$class}");
            },
            $this->allTemplatesDataProvider()
        );
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function allTemplatesDataProvider()
    {
        $blockClass = '';
        try {
            /** @var $website Website */
            Bootstrap::getObjectManager()->get(
                StoreManagerInterface::class
            )->getStore()->setWebsiteId(
                0
            );

            $templates = [];
            $skippedBlocks = $this->_getBlocksToSkip();
            foreach (Classes::collectModuleClasses('Block') as $blockClass => $module) {
                if (!in_array($module, $this->_getEnabledModules()) || in_array($blockClass, $skippedBlocks)) {
                    continue;
                }
                $class = new ReflectionClass($blockClass);
                if ($class->isAbstract() || !$class->isSubclassOf(\Magento\Framework\View\Element\Template::class)) {
                    continue;
                }

                $area = 'frontend';
                if ($module == 'Magento_Backend' || strpos(
                        $blockClass,
                        '\\Adminhtml\\'
                    ) !== false || strpos(
                        $blockClass,
                        '\\Backend\\'
                    ) !== false || $class->isSubclassOf(
                        Template::class
                    )
                ) {
                    $area = 'Adminhtml';
                }

                Bootstrap::getObjectManager()->get(
                    AreaList::class
                )->getArea(
                    $area
                )->load(
                    Area::PART_CONFIG
                );
                Bootstrap::getObjectManager()->get(
                    ScopeInterface::class
                )->setCurrentScope(
                    $area
                );
                Bootstrap::getObjectManager()->get(
                    State::class
                )->setAreaCode(
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
                $block = Bootstrap::getObjectManager()->create($blockClass);
                $template = $block->getTemplate();
                if ($template) {
                    $templates[$module . ', ' . $template . ', ' . $blockClass . ', ' . $area] = [
                        $module,
                        $template,
                        $blockClass,
                        $area,
                    ];
                }
            }
            return $templates;
        } catch (Exception $e) {
            trigger_error(
                "Corrupted data provider. Last known block instantiation attempt: '{$blockClass}'." .
                " Exception: {$e}",
                E_USER_ERROR
            );
        }
    }

    /**
     * @return array
     */
    protected function _getBlocksToSkip()
    {
        $result = [];
        foreach (glob(__DIR__ . '/_files/skip_template_blocks*.php') as $file) {
            $blocks = include $file;
            $result = array_merge($result, $blocks);
        }
        return array_combine($result, $result);
    }
}
