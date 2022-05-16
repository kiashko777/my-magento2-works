<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Widget\Controller\Adminhtml;

use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @magentoAppArea Adminhtml
 */
class WidgetTest extends AbstractBackendController
{
    /**
     * Partially covers \Magento\Widget\Block\Adminhtml\Widget\Options::_addField()
     */
    public function testLoadOptionsAction()
    {
        $this->getRequest()->setParam(
            'widget',
            '{"widget_type":"Magento\\\\Cms\\\\Block\\\\Widget\\\\Page\\\\link","values":{}}'
        );
        $this->dispatch('backend/admin/widget/loadOptions');
        $output = $this->getResponse()->getBody();
        //searching for label with text "CMS Page"
        $this->assertStringContainsString(
            'data-ui-id="wysiwyg-widget-options-fieldset-element-label-parameters-page-id-label" >' . '<span>CMS Page',
            $output
        );
    }
}
