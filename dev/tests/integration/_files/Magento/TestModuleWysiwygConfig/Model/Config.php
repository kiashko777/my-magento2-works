<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestModuleWysiwygConfig\Model;

use Magento\Cms\Model\Wysiwyg\DefaultConfigProvider;
use Magento\Framework\Data\Wysiwyg\ConfigProviderInterface;
use Magento\Framework\DataObject;

class Config implements ConfigProviderInterface
{
    /**
     * Configuration override for WYSIWYG height
     * @var string
     */
    const CONFIG_HEIGHT = 'something_else';

    /**
     * Configuration override for WYSIWYG content css
     * @var string
     */
    const CONFIG_CONTENT_CSS = 'something_else.css';

    /** @var DefaultConfigProvider */
    private $cmsConfigProvider;

    /**
     * @param DefaultConfigProvider $cmsConfigProvider
     */
    public function __construct(DefaultConfigProvider $cmsConfigProvider)
    {
        $this->cmsConfigProvider = $cmsConfigProvider;
    }

    /**
     * @inheritdoc
     */
    public function getConfig(DataObject $config): DataObject
    {
        //get default config
        $config = $this->cmsConfigProvider->getConfig($config);

        $config = $this->removeSpecialCharacterFromToolbar($config);

        $config = $this->modifyHeightAndContentCss($config);
        return $config;
    }

    /**
     * Remove the special character from the toolbar configuration
     *
     * @param DataObject $config
     * @return DataObject
     */
    private function removeSpecialCharacterFromToolbar(
        DataObject $config
    ): DataObject
    {
        $tinymce4 = $config->getData('tinymce4');
        if (isset($tinymce4['toolbar']) && isset($tinymce4['plugins'])) {
            $toolbar = $tinymce4['toolbar'];
            $plugins = $tinymce4['plugins'];
            $tinymce4['toolbar'] = str_replace('charmap', '', $toolbar);
            $tinymce4['plugins'] = str_replace('charmap', '', $plugins);
            $config->setData('tinymce4', $tinymce4);
        }
        return $config;
    }

    /**
     * Modify height and content_css in the config
     *
     * @param DataObject $config
     * @return DataObject
     */
    private function modifyHeightAndContentCss(DataObject $config): DataObject
    {
        return $config->addData(
            [
                'height' => self::CONFIG_HEIGHT,
                'content_css' => self::CONFIG_CONTENT_CSS
            ]
        );
    }
}
