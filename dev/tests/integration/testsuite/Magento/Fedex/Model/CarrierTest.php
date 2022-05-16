<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Fedex\Model;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class CarrierTest extends TestCase
{
    /**
     * @var Carrier
     */
    protected $_model;

    /**
     * @dataProvider getCodeDataProvider
     * @param string $type
     * @param int $expectedCount
     */
    public function testGetCode($type, $expectedCount)
    {
        $result = $this->_model->getCode($type);
        $this->assertCount($expectedCount, $result);
    }

    /**
     * Data Provider for testGetCode
     * @return array
     */
    public function getCodeDataProvider()
    {
        return [
            ['method', 21],
            ['dropoff', 5],
            ['packaging', 7],
            ['containers_filter', 4],
            ['delivery_confirmation_types', 4],
            ['unit_of_measure', 2],
        ];
    }

    /**
     * @dataProvider getCodeUnitOfMeasureDataProvider
     * @param string $code
     */
    public function testGetCodeUnitOfMeasure($code)
    {
        $result = $this->_model->getCode('unit_of_measure', $code);
        $this->assertNotEmpty($result);
    }

    /**
     * Data Provider for testGetCodeUnitOfMeasure
     * @return array
     */
    public function getCodeUnitOfMeasureDataProvider()
    {
        return [
            ['LB'],
            ['KG'],
        ];
    }

    protected function setUp(): void
    {
        $this->_model = Bootstrap::getObjectManager()->create(
            Carrier::class
        );
    }
}
