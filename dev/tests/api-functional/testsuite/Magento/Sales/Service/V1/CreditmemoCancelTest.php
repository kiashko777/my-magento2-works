<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Service\V1;

use Exception;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Class CreditmemoCancelTest
 */
class CreditmemoCancelTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';

    const SERVICE_NAME = 'salesCreditmemoManagementV1';

    const CREDITMEMO_INCREMENT_ID = '100000001';

    /**
     * @magentoApiDataFixture Magento/Sales/_files/creditmemo_with_list.php
     */
    public function testCreditmemoCancel()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('You can not cancel Credit Memo');

        $objectManager = Bootstrap::getObjectManager();

        /** @var Collection $creditmemoCollection */
        $creditmemoCollection = $objectManager->get(
            Collection::class
        );
        $creditmemo = $creditmemoCollection->getFirstItem();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/creditmemo/' . $creditmemo->getId(),
                'httpMethod' => Request::HTTP_METHOD_PUT,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'cancel',
            ],
        ];
        $requestData = ['id' => $creditmemo->getId()];
        $this->_webApiCall($serviceInfo, $requestData);
    }
}
