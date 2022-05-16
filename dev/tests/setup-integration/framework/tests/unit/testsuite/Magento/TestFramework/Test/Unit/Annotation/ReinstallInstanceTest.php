<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestFramework\Test\Unit\Annotation;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\TestFramework\Annotation\ReinstallInstance;
use Magento\TestFramework\Application;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for ReinstallInstance.
 *
 * @package Magento\TestFramework\Test\Unit\Annotation
 */
class ReinstallInstanceTest extends TestCase
{
    /**
     * @var ReinstallInstance
     */
    private $model;

    /**
     * @var ObjectManagerHelper
     */
    private $objectManagerHelper;

    /**
     * @var Application|MockObject
     */
    private $applicationMock;

    public function testEndTest()
    {
        $this->applicationMock->expects($this->once())
            ->method('cleanup');
        $this->model->endTest();
    }

    protected function setUp(): void
    {
        $this->applicationMock = $this
            ->getMockBuilder(Application::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->model = $this->objectManagerHelper->getObject(
            ReinstallInstance::class,
            [
                'application' => $this->applicationMock
            ]
        );
    }
}
