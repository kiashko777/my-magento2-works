<?php
declare(strict_types=1);

namespace Devall\Customform\Model;

use Devall\Customform\Api\Data\CustomformInterface;
use Devall\Customform\Api\Data\CustomformInterfaceFactory;
use Devall\Customform\Model\ResourceModel\Customform\Collection;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

class Customform extends \Magento\Framework\Model\AbstractModel
{

    protected $_eventPrefix = 'devall_customform';
    protected $dataObjectHelper;

    protected $customformDataFactory;


    /**
     * @param Context $context
     * @param Registry $registry
     * @param CustomformInterfaceFactory $customformDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ResourceModel\Customform $resource
     * @param Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context                                                      $context,
        Registry                                                     $registry,
        CustomformInterfaceFactory                                   $customformDataFactory,
        DataObjectHelper                                             $dataObjectHelper,
        \Devall\Customform\Model\ResourceModel\Customform            $resource,
        \Devall\Customform\Model\ResourceModel\Customform\Collection $resourceCollection,
        array                                                        $data = []
    )
    {
        $this->customformDataFactory = $customformDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve customform model with customform data
     * @return CustomformInterface
     */
    public function getDataModel()
    {
        $customformData = $this->getData();

        $customformDataObject = $this->customformDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $customformDataObject,
            $customformData,
            CustomformInterface::class
        );

        return $customformDataObject;
    }
}
