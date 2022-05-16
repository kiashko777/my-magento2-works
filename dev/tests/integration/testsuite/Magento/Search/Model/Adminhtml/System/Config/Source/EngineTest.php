<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Search\Model\Adminhtml\System\Config\Source;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class EngineTest extends TestCase
{
    /**
     * @var Engine
     */
    protected $_model;

    public function testToOptionArray()
    {
        $options = $this->_model->toOptionArray();
        $this->assertNotEmpty($options);

        foreach ($options as $option) {
            $this->assertArrayHasKey('label', $option);
            $this->assertArrayHasKey('value', $option);
        }
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->_model = Bootstrap::getObjectManager()->create(
            Engine::class
        );
    }
}
