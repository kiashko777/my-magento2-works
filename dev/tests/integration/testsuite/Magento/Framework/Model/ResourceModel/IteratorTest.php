<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\ResourceModel\Store\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class IteratorTest extends TestCase
{
    /**
     * @var Iterator
     */
    protected $_model;

    /**
     * Counter for testing walk() callback
     *
     * @var int
     */
    protected $_callbackCounter = 0;

    public function testWalk()
    {
        $collection = Bootstrap::getObjectManager()->create(
            Collection::class
        );
        $this->_model->walk($collection->getSelect(), [[$this, 'walkCallback']]);
        $this->assertGreaterThan(0, $this->_callbackCounter);
    }

    /**
     * Helper callback for testWalk()
     *
     * @param array $data
     * @return bool
     */
    public function walkCallback($data)
    {
        $this->_callbackCounter = $data['idx'];
        return true;
    }

    /**
     */
    public function testWalkException()
    {
        $this->expectException(LocalizedException::class);

        $this->_model->walk('test', [[$this, 'walkCallback']]);
    }

    protected function setUp(): void
    {
        $this->_model = Bootstrap::getObjectManager()->create(
            Iterator::class
        );
    }
}
