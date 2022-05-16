<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestFramework;

use Exception;
use InvalidArgumentException;
use Magento\Framework\Model\AbstractModel;
use PHPUnit\Framework\Assert;

/**
 * Class that implements CRUD tests for \Magento\Framework\Model\AbstractModel based objects
 */
class Entity
{
    /**
     * @var AbstractModel
     */
    protected $_model;

    /**
     * @var array
     */
    protected $_updateData;

    /**
     * @var string
     */
    protected $_modelClass;

    /**
     * @param AbstractModel $model
     * @param array $updateData
     * @param string|null $modelClass Class of a model to use when creating new instances, or NULL for auto-detection
     * @throws InvalidArgumentException
     */
    public function __construct(AbstractModel $model, array $updateData, $modelClass = null)
    {
        $this->_model = $model;
        $this->_updateData = $updateData;
        if ($modelClass) {
            if (!$model instanceof $modelClass) {
                throw new InvalidArgumentException("Class '$modelClass' is irrelevant to the tested model.");
            }
            $this->_modelClass = $modelClass;
        } else {
            $this->_modelClass = get_class($this->_model);
        }
    }

    /**
     * Test Create -> Read -> Update -> Delete operations
     */
    public function testCrud()
    {
        $this->_testCreate();
        try {
            $this->_testRead();
            $this->_testUpdate();
            $this->_testDelete();
        } catch (Exception $e) {
            $this->_model->delete();
            throw $e;
        }
    }

    protected function _testCreate()
    {
        if ($this->_model->getId()) {
            Assert::fail("Can't run creation test for models with defined id");
        }
        $this->_model->save();
        Assert::assertNotEmpty($this->_model->getId(), 'CRUD Create error');
    }

    protected function _testRead()
    {
        $model = $this->_getEmptyModel();
        $model->load($this->_model->getId());
        Assert::assertEquals($this->_model->getId(), $model->getId(), 'CRUD Read error');
    }

    /**
     * Retrieve new instance of not yet loaded model
     *
     * @return AbstractModel
     */
    protected function _getEmptyModel()
    {
        return \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create($this->_modelClass);
    }

    protected function _testUpdate()
    {
        foreach ($this->_updateData as $key => $value) {
            $this->_model->setDataUsingMethod($key, $value);
        }
        $this->_model->save();

        $model = $this->_getEmptyModel();
        $model->load($this->_model->getId());
        foreach ($this->_updateData as $key => $value) {
            Assert::assertEquals(
                $value,
                $model->getDataUsingMethod($key),
                'CRUD Update "' . $key . '" error'
            );
        }
    }

    protected function _testDelete()
    {
        $modelId = $this->_model->getId();
        $this->_model->delete();

        $model = $this->_getEmptyModel();
        $model->load($modelId);
        Assert::assertEmpty($model->getId(), 'CRUD Delete error');
    }
}
