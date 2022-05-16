<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test\Integrity\Modular;

use Magento\Backend\Model\Menu\Config\Reader;
use Magento\Framework\Config\Dom\UrnResolver;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class MenuConfigFilesTest extends TestCase
{
    /**
     * @var Reader
     */
    protected $_model;

    public function testValidateMenuFiles()
    {
        $this->_model->read('Adminhtml');
    }

    protected function setUp(): void
    {
        $urnResolver = new UrnResolver();
        $schemaFile = $urnResolver->getRealPath('urn:magento:module:Magento_Backend:etc/menu.xsd');
        $this->_model = Bootstrap::getObjectManager()->create(
            Reader::class,
            ['perFileSchema' => $schemaFile, 'isValidated' => true]
        );
    }
}
