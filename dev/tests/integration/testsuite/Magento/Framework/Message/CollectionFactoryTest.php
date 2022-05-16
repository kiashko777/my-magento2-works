<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Message;

use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * \Magento\Framework\Message\CollectionFactory test case
 */
class CollectionFactoryTest extends TestCase
{
    /**
     * @var CollectionFactory
     */
    protected $model;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    public function testCreate()
    {
        $message = $this->model->create();
        $this->assertInstanceOf(Collection::class, $message);
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->model = $this->objectManager->create(CollectionFactory::class);
    }
}
