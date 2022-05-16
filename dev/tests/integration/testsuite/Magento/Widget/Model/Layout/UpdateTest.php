<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Widget\Model\Layout;

use Magento\TestFramework\Entity;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class UpdateTest extends TestCase
{
    /**
     * @var Update
     */
    protected $_model;

    public function testConstructor()
    {
        $this->_model = Bootstrap::getObjectManager()->create(
            Update::class
        );
        $this->assertInstanceOf(
            \Magento\Widget\Model\ResourceModel\Layout\Update::class,
            $this->_model->getResource()
        );
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testCrud()
    {
        $this->_model->setData(['handle' => 'default', 'xml' => '<layout/>', 'sort_order' => 123]);
        $entityHelper = new Entity(
            $this->_model,
            ['handle' => 'custom', 'xml' => '<layout version="0.1.0"/>', 'sort_order' => 456]
        );
        $entityHelper->testCrud();
    }

    protected function setUp(): void
    {
        $this->_model = Bootstrap::getObjectManager()->create(
            Update::class
        );
    }
}
