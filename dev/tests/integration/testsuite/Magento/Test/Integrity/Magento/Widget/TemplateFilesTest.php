<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test\Integrity\Magento\Widget;

use Magento\Framework\View\Element\BlockFactory;
use Magento\Framework\View\Element\Template;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Widget\Model\Widget;
use Magento\Widget\Model\Widget\Instance;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea frontend
 */
class TemplateFilesTest extends TestCase
{
    /**
     * Check if all the declared widget templates actually exist
     *
     * @param string $class
     * @param string $template
     * @dataProvider widgetTemplatesDataProvider
     */
    public function testWidgetTemplates($class, $template)
    {
        /** @var $blockFactory BlockFactory */
        $blockFactory = Bootstrap::getObjectManager()->get(
            BlockFactory::class
        );
        /** @var Template $block */
        $block = $blockFactory->createBlock($class);
        $this->assertInstanceOf(Template::class, $block);
        $block->setTemplate((string)$template);
        $this->assertFileExists($block->getTemplateFile());
    }

    /**
     * Collect all declared widget blocks and templates
     *
     * @return array
     */
    public function widgetTemplatesDataProvider()
    {
        $result = [];
        /** @var $model Widget */
        $model = Bootstrap::getObjectManager()->create(
            Widget::class
        );
        foreach ($model->getWidgetsArray() as $row) {
            /** @var $instance Instance */
            $instance = Bootstrap::getObjectManager()->create(
                Instance::class
            );
            $config = $instance->setType($row['type'])->getWidgetConfigAsArray();
            $class = $row['type'];
            if (is_subclass_of($class, Template::class)) {
                if (isset(
                        $config['parameters']
                    ) && isset(
                        $config['parameters']['template']
                    ) && isset(
                        $config['parameters']['template']['values']
                    )
                ) {
                    $templates = $config['parameters']['template']['values'];
                    foreach ($templates as $template) {
                        if (isset($template['value'])) {
                            $result[] = [$class, (string)$template['value']];
                        }
                    }
                }
            }
        }
        return $result;
    }
}
