<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Service\V1;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Sales\Api\Data\CreditmemoCommentInterface as Comment;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Class CreditmemoAddCommentTest
 */
class CreditmemoAddCommentTest extends WebapiAbstract
{
    /**
     * Service read name
     */
    const SERVICE_READ_NAME = 'salesCreditmemoCommentRepositoryV1';

    /**
     * Service version
     */
    const SERVICE_VERSION = 'V1';

    /**
     * Creditmemo increment id
     */
    const CREDITMEMO_INCREMENT_ID = '100000001';

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Test creditmemo add comment service
     *
     * @magentoApiDataFixture Magento/Sales/_files/creditmemo_with_list.php
     */
    public function testCreditmemoAddComment()
    {
        /** @var Collection $creditmemoCollection */
        $creditmemoCollection =
            $this->objectManager->get(Collection::class);
        $creditmemo = $creditmemoCollection->getFirstItem();

        $commentData = [
            Comment::COMMENT => 'Hello world!',
            Comment::ENTITY_ID => null,
            Comment::CREATED_AT => null,
            Comment::PARENT_ID => $creditmemo->getId(),
            Comment::IS_VISIBLE_ON_FRONT => 1,
            Comment::IS_CUSTOMER_NOTIFIED => 1,
        ];

        $requestData = ['entity' => $commentData];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/creditmemo/' . $creditmemo->getId() . '/comments',
                'httpMethod' => Request::HTTP_METHOD_POST,
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'save',
            ],
        ];

        $result = $this->_webApiCall($serviceInfo, $requestData);

        self::assertNotEmpty($result);
        self::assertNotEmpty($result[Comment::ENTITY_ID]);
        self::assertEquals($creditmemo->getId(), $result[Comment::PARENT_ID]);
    }

    /**
     * Set up
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }
}
