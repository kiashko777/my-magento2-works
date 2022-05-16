<?php
/**
 * Test configuration of Online Shipping carriers
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test\Integrity\Modular;

use Magento\Config\Model\Config\Structure\Reader;
use Magento\Framework\Config\Dom\UrnResolver;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class CarrierConfigFilesTest extends TestCase
{
    /**
     * @var Reader
     */
    protected $_reader;

    /**
     * Tests that all source_models used in shipping are valid
     */
    public function testValidateShippingSourceModels()
    {
        $config = $this->_reader->read('Adminhtml');

        $carriers = $config['config']['system']['sections']['carriers']['children'];
        foreach ($carriers as $carrier) {
            foreach ($carrier['children'] as $field) {
                if (isset($field['source_model'])) {
                    $model = $field['source_model'];
                    Bootstrap::getObjectManager()->create($model);
                }
            }
        }
    }

    protected function setUp(): void
    {
        $urnResolver = new UrnResolver();
        $schemaFile = $urnResolver->getRealPath('urn:magento:module:Magento_Config:etc/system.xsd');
        $this->_reader = Bootstrap::getObjectManager()->create(
            Reader::class,
            ['perFileSchema' => $schemaFile, 'isValidated' => true]
        );
    }
}
