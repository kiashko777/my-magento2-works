<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test;

use InvalidArgumentException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\TestFramework\Entity;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EntityTest extends TestCase
{
    /**
     * @var AbstractModel|MockObject
     */
    protected $_model;

    /**
     * Callback for save method in mocked model
     *
     * @throws LocalizedException
     */
    public function saveModelAndFailOnUpdate()
    {
        if (!$this->_model->getId()) {
            $this->saveModelSuccessfully();
        } else {
            throw new LocalizedException(__('Synthetic model update failure.'));
        }
    }

    /**
     * Callback for save method in mocked model
     */
    public function saveModelSuccessfully()
    {
        $this->_model->setId('1');
    }

    /**
     * Callback for delete method in mocked model
     */
    public function deleteModelSuccessfully()
    {
        $this->_model->setId(null);
    }

    /**
     */
    public function testConstructorIrrelevantModelClass()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Class \'stdClass\' is irrelevant to the tested model');

        new Entity($this->_model, [], 'stdClass');
    }

    public function crudDataProvider()
    {
        return [
            'successful CRUD' => ['saveModelSuccessfully'],
            'cleanup on update error' => [
                'saveModelAndFailOnUpdate',
                LocalizedException::class
            ]
        ];
    }

    /**
     * @dataProvider crudDataProvider
     */
    public function testTestCrud($saveCallback, $expectedException = null)
    {
        if ($expectedException != null) {
            $this->expectException($expectedException);
        }

        $this->_model->expects($this->atLeastOnce())
            ->method('load');
        $this->_model->expects($this->atLeastOnce())
            ->method('save')
            ->willReturnCallback([$this, $saveCallback]);
        /* It's important that 'delete' should be always called to guarantee the cleanup */
        $this->_model->expects(
            $this->atLeastOnce()
        )->method(
            'delete'
        )->willReturnCallback(
            [$this, 'deleteModelSuccessfully']
        );

        $this->_model->expects($this->any())->method('getIdFieldName')->willReturn('id');

        $test = $this->getMockBuilder(Entity::class)
            ->setMethods(['_getEmptyModel'])
            ->setConstructorArgs([$this->_model, ['test' => 'test']])
            ->getMock();

        $test->expects($this->any())->method('_getEmptyModel')->willReturn($this->_model);
        $test->testCrud();
    }

    protected function setUp(): void
    {
        $this->_model = $this->createPartialMock(
            AbstractModel::class,
            ['load', 'save', 'delete', 'getIdFieldName', '__wakeup']
        );
    }
}
