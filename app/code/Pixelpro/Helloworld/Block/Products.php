<?php

namespace Pixelpro\Helloworld\Block;

use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\View\Element\Template;

class Products extends Template
{
    protected CollectionFactory $_productCollectionFactory;

    public function __construct(
        Context                        $context,
        CollectionFactory $productCollectionFactory,
        array                                                          $data = []
    )
    {
        $this->_productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $data);
    }

    public function getProductCollection()
    {
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->setPageSize(3); // fetching only 3 products
        return $collection;
    }
}

