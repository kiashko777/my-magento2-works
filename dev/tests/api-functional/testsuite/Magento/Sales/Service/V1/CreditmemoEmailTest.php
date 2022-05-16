<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Service\V1;

use Magento\Framework\Webapi\Rest\Request;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Class CreditmemoEmailTest
 */
class CreditmemoEmailTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';

    const SERVICE_NAME = 'salesCreditmemoManagementV1';

    const CREDITMEMO_INCREMENT_ID = '100000001';

    /**
     * @magentoApiDataFixture Magento/Sales/_files/creditmemo_with_list.php
     */
    public function testCreditmemoEmail()
    {
        $objectManager = Bootstrap::getObjectManager();

        /** @var Collection $creditmemoCollection */
        $creditmemoCollection = $objectManager->get(
            Collection::class
        );
        $creditmemo = $creditmemoCollection->getFirstItem();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/creditmemo/' . $creditmemo->getId() . '/emails',
                'httpMethod' => Request::HTTP_METHOD_POST,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'notify',
            ],
        ];
        $requestData = ['id' => $creditmemo->getId()];
        $this->_webApiCall($serviceInfo, $requestData);
    }
}
