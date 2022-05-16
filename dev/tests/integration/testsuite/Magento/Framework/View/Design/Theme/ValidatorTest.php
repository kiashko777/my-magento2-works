<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test theme data validator
 */

namespace Magento\Framework\View\Design\Theme;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\View\Design\ThemeInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    /**
     * Test validator with valid data
     */
    public function testValidateWithValidData()
    {
        /** @var $validator Validator */
        $validator = Bootstrap::getObjectManager()->create(
            Validator::class
        );

        $themeModel = $this->_getThemeModel();
        $themeModel->setData($this->_getThemeValidData());

        $this->assertTrue($validator->validate($themeModel));
    }

    /**
     * Get theme model
     *
     * @return AbstractModel
     */
    protected function _getThemeModel()
    {
        return Bootstrap::getObjectManager()->create(
            ThemeInterface::class
        );
    }

    /**
     * Get theme valid data
     *
     * @return array
     */
    protected function _getThemeValidData()
    {
        return [
            'theme_code' => 'space',
            'theme_title' => 'Space theme',
            'parent_theme' => null,
            'theme_path' => 'default/space',
            'preview_image' => 'images/preview.png'
        ];
    }

    /**
     * Test validator with invalid data
     */
    public function testValidateWithInvalidData()
    {
        /** @var $validator Validator */
        $validator = Bootstrap::getObjectManager()->create(
            Validator::class
        );

        $themeModel = $this->_getThemeModel();
        $themeModel->setData($this->_getThemeInvalidData());

        $this->assertFalse($validator->validate($themeModel));
    }

    /**
     * Get theme invalid data
     *
     * @return array
     */
    protected function _getThemeInvalidData()
    {
        return [
            'theme_code' => 'space',
            'theme_title' => '',
            'parent_theme' => null,
            'theme_path' => 'default/space',
            'preview_image' => 'images/preview.png'
        ];
    }
}
