<?php
/**
 * Test services for name collisions.
 *
 * Let we have two service interfaces called Foo\Bar\Service\SomeBazV1Interface and Foo\Bar\Service\Some\BazV1Interface.
 * Given current name generation logic both are going to be translated to BarSomeBazV1. This test checks such things
 * are not going to happen.
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Webapi;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\Webapi\Model\Config;
use Magento\Webapi\Model\Config\Converter;
use Magento\Webapi\Model\ServiceMetadata;
use PHPUnit\Framework\TestCase;

class ServiceNameCollisionTest extends TestCase
{
    /**
     * Test there are no collisions between service names.
     *
     * @see \Magento\Webapi\Model\Soap\Config::getServiceName()
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function testServiceNameCollisions()
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var ServiceMetadata $serviceMetadata */
        $serviceMetadata = $objectManager->get(ServiceMetadata::class);
        /** @var Config $webapiConfig */
        $webapiConfig = $objectManager->get(Config::class);
        $serviceNames = [];

        foreach ($webapiConfig->getServices()[Converter::KEY_SERVICES] as $serviceClassName => $serviceVersionData) {
            foreach ($serviceVersionData as $version => $serviceData) {
                $newServiceName = $serviceMetadata->getServiceName($serviceClassName, $version);
                $this->assertFalse(in_array($newServiceName, $serviceNames));
                $serviceNames[] = $newServiceName;
            }
        }
    }
}
