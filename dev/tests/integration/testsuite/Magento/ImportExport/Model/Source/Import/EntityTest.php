<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test class for entity source model \Magento\ImportExport\Model\Source\Import\Entity
 */

namespace Magento\ImportExport\Model\Source\Import;

use Magento\ImportExport\Model\Import\ConfigInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EntityTest extends TestCase
{
    /**
     * @var Entity
     */
    protected $_model;

    /**
     * @var MockObject
     */
    protected $_importConfigMock;

    public function testToOptionArray()
    {
        $entities = [
            'entity_name_1' => ['name' => 'entity_name_1', 'label' => 'entity_label_1'],
            'entity_name_2' => ['name' => 'entity_name_2', 'label' => 'entity_label_2'],
        ];
        $expectedResult = [
            ['label' => __('-- Please Select --'), 'value' => ''],
            ['label' => __('entity_label_1'), 'value' => 'entity_name_1'],
            ['label' => __('entity_label_2'), 'value' => 'entity_name_2'],
        ];
        $this->_importConfigMock->expects($this->any())->method('getEntities')->willReturn($entities);
        $this->assertEquals($expectedResult, $this->_model->toOptionArray());
    }

    protected function setUp(): void
    {
        $this->_importConfigMock = $this->createMock(ConfigInterface::class);
        $this->_model = new Entity($this->_importConfigMock);
    }
}
