<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Theme\Model\Theme;

use Magento\Framework\View\Design\ThemeInterface;
use Magento\TestFramework\Entity;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Theme\Model\Theme;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    /**
     * @var File
     */
    protected $_model;

    /**
     * @var array
     */
    protected $_data = [];

    /**
     * @var Theme
     */
    protected $_theme;

    /**
     * Test crud operations for theme files model using valid data
     */
    public function testCrud()
    {
        $this->_model->setData($this->_data);

        $crud = new Entity($this->_model, ['file_path' => 'rename.css']);
        $crud->testCrud();
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->_model = $objectManager->create(File::class);
        /** @var $themeModel ThemeInterface */
        $themeModel = $objectManager->create(ThemeInterface::class);
        $this->_theme = $themeModel->getCollection()->getFirstItem();
        $this->_data = [
            'file_path' => 'main.css',
            'file_type' => 'css',
            'content' => 'content files',
            'order' => 0,
            'theme' => $this->_theme,
            'theme_id' => $this->_theme->getId(),
        ];
    }

    protected function tearDown(): void
    {
        $this->_model = null;
        $this->_data = [];
        $this->_theme = null;
    }
}
