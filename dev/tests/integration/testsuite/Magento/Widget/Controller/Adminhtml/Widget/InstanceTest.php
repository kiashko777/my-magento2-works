<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Widget\Controller\Adminhtml\Widget;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Cms\Block\Widget\Page\Link;
use Magento\Framework\App\Area;
use Magento\Framework\View\DesignInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @magentoAppArea Adminhtml
 */
class InstanceTest extends AbstractBackendController
{
    public function testEditAction()
    {
        $this->dispatch('backend/admin/widget_instance/edit');
        $this->assertRegExp(
            '/<option value="cms_page_link".*?selected="selected"\>/is',
            $this->getResponse()->getBody()
        );
    }

    public function testBlocksAction()
    {
        Bootstrap::getInstance()
            ->loadArea(Area::AREA_FRONTEND);
        $theme = Bootstrap::getObjectManager()->get(
            DesignInterface::class
        )->setDefaultDesignTheme()->getDesignTheme();
        $this->getRequest()->setParam('theme_id', $theme->getId());
        $this->dispatch('backend/admin/widget_instance/blocks');
        $this->assertStringStartsWith('<select name="block" id=""', $this->getResponse()->getBody());
    }

    public function testTemplateAction()
    {
        $this->getRequest()->setMethod('POST');
        $this->dispatch('backend/admin/widget_instance/template');
        $this->assertStringStartsWith('<select name="template" id=""', $this->getResponse()->getBody());
    }

    protected function setUp(): void
    {
        parent::setUp();

        Bootstrap::getInstance()
            ->loadArea(FrontNameResolver::AREA_CODE);

        $theme = Bootstrap::getObjectManager()->get(
            DesignInterface::class
        )->setDefaultDesignTheme()->getDesignTheme();
        $type = Link::class;
        /** @var $model \Magento\Widget\Model\Widget\Instance */
        $model = Bootstrap::getObjectManager()->create(
            \Magento\Widget\Model\Widget\Instance::class
        );
        $code = $model->setType($type)->getWidgetReference('type', $type, 'code');
        $this->getRequest()->setParam('code', $code);
        $this->getRequest()->setParam('theme_id', $theme->getId());
    }
}
