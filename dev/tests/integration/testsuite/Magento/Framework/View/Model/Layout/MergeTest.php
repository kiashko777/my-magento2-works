<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\View\Model\Layout;

use Magento\Framework\View\Design\ThemeInterface;
use Magento\Framework\View\Layout\LayoutCacheKeyInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Widget\Model\Layout\Link;
use Magento\Widget\Model\Layout\Update;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MergeTest extends TestCase
{
    /**
     * Fixture XML instruction(s) to be used in tests
     */
    const FIXTURE_LAYOUT_XML
        = '<block class="Magento\Framework\View\Element\Template" template="Magento_Framework::fixture.phtml"/>';

    /**
     * @var Merge
     */
    protected $model;

    /**
     * @var LayoutCacheKeyInterface|MockObject
     */
    protected $layoutCacheKeyMock;

    public function testLoadDbApp()
    {
        $this->assertEmpty($this->model->getHandles());
        $this->assertEmpty($this->model->asString());
        $handles = ['fixture_handle_one', 'fixture_handle_two'];
        $this->model->load($handles);
        $expectedResult = '
            <root>
                <body>
                    <block class="Magento\Framework\View\Element\Template"
                           template="Magento_Framework::fixture_template_one.phtml"/>
                </body>
                <body>
                    <block class="Magento\Framework\View\Element\Template"
                           template="Magento_Framework::fixture_template_two.phtml"/>
                </body>
            </root>
        ';
        $actualResult = '<root>' . $this->model->asString() . '</root>';
        $this->assertXmlStringEqualsXmlString($expectedResult, $actualResult);
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();

        /** @var $theme ThemeInterface */
        $theme = $objectManager->create(ThemeInterface::class);
        $theme->load(1);

        /** @var $layoutUpdate1 Update */
        $layoutUpdate1 = Bootstrap::getObjectManager()->create(
            Update::class
        );
        $layoutUpdate1->setHandle('fixture_handle_one');
        $layoutUpdate1->setXml(
            '<body>
                <block class="Magento\Framework\View\Element\Template"
                       template="Magento_Framework::fixture_template_one.phtml"/>
            </body>'
        );
        $layoutUpdate1->setHasDataChanges(true);
        $layoutUpdate1->save();
        $link1 = $objectManager->create(Link::class);
        $link1->setThemeId($theme->getId());
        $link1->setLayoutUpdateId($layoutUpdate1->getId());
        $link1->save();

        /** @var $layoutUpdate2 Update */
        $layoutUpdate2 = Bootstrap::getObjectManager()->create(
            Update::class
        );
        $layoutUpdate2->setHandle('fixture_handle_two');
        $layoutUpdate2->setXml(
            '<body>
                <block class="Magento\Framework\View\Element\Template"
                       template="Magento_Framework::fixture_template_two.phtml"/>
            </body>'
        );
        $layoutUpdate2->setHasDataChanges(true);
        $layoutUpdate2->save($layoutUpdate2);
        $link2 = $objectManager->create(Link::class);
        $link2->setThemeId($theme->getId());
        $link2->setLayoutUpdateId($layoutUpdate2->getId());
        $link2->save();

        $this->layoutCacheKeyMock = $this->getMockForAbstractClass(LayoutCacheKeyInterface::class);
        $this->layoutCacheKeyMock->expects($this->any())
            ->method('getCacheKeys')
            ->willReturn([]);

        $this->model = $objectManager->create(
            Merge::class,
            [
                'theme' => $theme,
                'layoutCacheKey' => $this->layoutCacheKeyMock,
            ]
        );
    }
}
