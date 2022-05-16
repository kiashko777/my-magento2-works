<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Webapi;

use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\Webapi\Adapter\Soap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Test WSDL generation mechanisms.
 */
class CustomAttributeTypeWsdlGenerationTest extends WebapiAbstract
{
    /** @var string */
    protected $_baseUrl = TESTS_BASE_URL;

    /** @var string */
    protected $_storeCode;

    /** @var string */
    protected $_soapUrl;

    public function testCustomAttributeTypesInWsdl()
    {
        /** @var $soapAdapter Soap */
        $soapAdapter = $this->_getWebApiAdapter(self::ADAPTER_SOAP);
        $soapClient = $soapAdapter->instantiateSoapClient($this->_soapUrl);
        $types = $soapClient->getTypes();

        $testCustomTypeCount = 0;
        foreach ($types as $type) {
            if (strpos($type, 'TestModuleMSCDataCustomAttributeDataObjectInterface') !== false ||
                strpos($type, 'TestModuleMSCDataCustomAttributeNestedDataObjectInterface') !== false
            ) {
                $testCustomTypeCount++;
            }
        }

        $this->assertEquals(
            2,
            $testCustomTypeCount,
            'Incorrect count for Custom attribute types. Found "' . $testCustomTypeCount . ' type(s)" expected 2.'
        );
    }

    protected function setUp(): void
    {
        $this->_markTestAsSoapOnly("WSDL generation tests are intended to be executed for SOAP adapter only.");
        $this->_storeCode = Bootstrap::getObjectManager()->get(StoreManagerInterface::class)
            ->getStore()->getCode();
        $this->_soapUrl = "{$this->_baseUrl}/soap/{$this->_storeCode}?wsdl=1&services=testModuleMSCAllSoapAndRestV1";
        parent::setUp();
    }
}
