<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestFramework\Utility;

use Magento\Catalog\Api\Data\ProductLinkInterfaceFactory;
use Magento\Catalog\Model\Indexer\Product\Flat\Table\BuilderInterfaceFactory;
use Magento\Catalog\Model\Product;
use Magento\Customer\Api\CustomerRepositoryInterfaceFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\UrlFactory;

class Foo
{
    /**
     * Constructor
     *
     * @param CustomerRepositoryInterfaceFactory|null $customerRepositoryFactory
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        CustomerRepositoryInterfaceFactory $customerRepositoryFactory = null
    )
    {
    }

    /**
     * @return BuilderInterfaceFactory
     */
    public function getBuilderFactory()
    {
        return ObjectManager::getInstance()->get(
            BuilderInterfaceFactory::class
        );
    }

    /**
     * @return BarFactory
     */
    public function getBarFactory()
    {
        return ObjectManager::getInstance()->get(BarFactory::class);
    }

    /**
     * @return PartialNamespace\BarFactory
     */
    public function getPartialNamespaceBarFactory()
    {
        return ObjectManager::getInstance()->get(PartialNamespace\BarFactory::class);
    }

    /**
     * @return UrlFactory
     */
    public function getUrlFactory()
    {
        return ObjectManager::getInstance()->get(UrlFactory::class);
    }

    /**
     * @return Product\OptionFactory
     */
    public function getOptionFactory()
    {
        return ObjectManager::getInstance()->get(Product\OptionFactory::class);
    }

    /**
     * @return ProductLinkInterfaceFactory
     */
    public function getProductLinkFactory()
    {
        return ObjectManager::getInstance()
            ->get(
                ProductLinkInterfaceFactory::class
            );
    }

    /**
     * @return CustomerRepositoryInterfaceFactory
     */
    public function getCustomerRepositoryFactory()
    {
        return ObjectManager::getInstance()->get(
            CustomerRepositoryInterfaceFactory::class
        );
    }
}
