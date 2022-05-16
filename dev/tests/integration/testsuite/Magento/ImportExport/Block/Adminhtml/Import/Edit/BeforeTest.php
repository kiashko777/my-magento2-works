<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test class for \Magento\ImportExport\Block\Adminhtml\Import\Edit\Before
 */

namespace Magento\ImportExport\Block\Adminhtml\Import\Edit;

use Magento\ImportExport\Model\Import;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class BeforeTest extends TestCase
{
    /**
     * Test model
     *
     * @var Before
     */
    protected $_model;

    /**
     * Source entity behaviors
     *
     * @var array
     */
    protected $_sourceEntities = [
        'entity_1' => ['code' => 'behavior_1', 'token' => 'Some_Random_First_Class'],
        'entity_2' => ['code' => 'behavior_2', 'token' => 'Some_Random_Second_Class'],
    ];

    /**
     * Expected entity behaviors
     *
     * @var array
     */
    protected $_expectedEntities = ['entity_1' => 'behavior_1', 'entity_2' => 'behavior_2'];

    /**
     * Source unique behaviors
     *
     * @var array
     */
    protected $_sourceBehaviors = [
        'behavior_1' => 'Some_Random_First_Class',
        'behavior_2' => 'Some_Random_Second_Class',
    ];

    /**
     * Expected unique behaviors
     *
     * @var array
     */
    protected $_expectedBehaviors = ['behavior_1', 'behavior_2'];

    /**
     * Test for getEntityBehaviors method
     *
     * @covers \Magento\ImportExport\Block\Adminhtml\Import\Edit\Before::getEntityBehaviors
     */
    public function testGetEntityBehaviors()
    {
        $actualEntities = $this->_model->getEntityBehaviors();
        $expectedEntities = json_encode($this->_expectedEntities);
        $this->assertEquals($expectedEntities, $actualEntities);
    }

    /**
     * Test for getUniqueBehaviors method
     *
     * @covers \Magento\ImportExport\Block\Adminhtml\Import\Edit\Before::getUniqueBehaviors
     */
    public function testGetUniqueBehaviors()
    {
        $actualBehaviors = $this->_model->getUniqueBehaviors();
        $expectedBehaviors = json_encode($this->_expectedBehaviors);
        $this->assertEquals($expectedBehaviors, $actualBehaviors);
    }

    protected function setUp(): void
    {
        $importModel = $this->createPartialMock(
            Import::class,
            ['getEntityBehaviors', 'getUniqueEntityBehaviors']
        );
        $importModel->expects(
            $this->any()
        )->method(
            'getEntityBehaviors'
        )->willReturn(
            $this->_sourceEntities
        );
        $importModel->expects(
            $this->any()
        )->method(
            'getUniqueEntityBehaviors'
        )->willReturn(
            $this->_sourceBehaviors
        );

        $objectManager = Bootstrap::getObjectManager();
        $this->_model = $objectManager->create(
            Before::class,
            [
                'importModel' => $importModel,
            ]
        );
    }
}
