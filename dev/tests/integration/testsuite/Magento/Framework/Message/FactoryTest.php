<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Message;

use InvalidArgumentException;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * \Magento\Framework\Message\Factory test case
 */
class FactoryTest extends TestCase
{
    /**
     * @var Factory
     */
    protected $model;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @dataProvider createProvider
     */
    public function testCreate($messageType)
    {
        $message = $this->model->create($messageType, 'some text');
        $this->assertInstanceOf(MessageInterface::class, $message);
    }

    public function createProvider()
    {
        return [
            [MessageInterface::TYPE_SUCCESS],
            [MessageInterface::TYPE_NOTICE],
            [MessageInterface::TYPE_WARNING],
            [MessageInterface::TYPE_ERROR]
        ];
    }

    /**
     */
    public function testCreateWrong()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Wrong message type');

        $this->model->create('Wrong', 'some text');
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->model = $this->objectManager->create(Factory::class);
    }
}
