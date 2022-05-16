<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Webapi\Controller;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Webapi\Request;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Webapi\Model\Soap\Server;
use PHPUnit\Framework\TestCase;

/**
 * Test for Magento\Webapi\Controller\Soap class.
 */
class SoapTest extends TestCase
{
    /**
     * @var Soap
     */
    protected $soapController;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Get the public wsdl with anonymous credentials
     *
     * @return void
     */
    public function testDispatchWsdlRequest(): void
    {
        $request = $this->objectManager->get(Request::class);
        $request->setParam(Server::REQUEST_PARAM_LIST_WSDL, true);
        $response = $this->soapController->dispatch($request);
        $decodedWsdl = json_decode($response->getContent(), true);

        $this->assertWsdlServices($decodedWsdl);
    }

    /**
     * Check wsdl available methods.
     *
     * @param array $decodedWsdl
     *
     * @return void
     */
    protected function assertWsdlServices(array $decodedWsdl): void
    {
        $this->assertArrayHasKey('customerAccountManagementV1', $decodedWsdl);
        $this->assertArrayHasKey('integrationAdminTokenServiceV1', $decodedWsdl);
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->soapController = $this->objectManager->get(Soap::class);
    }
}
