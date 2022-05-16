<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Bundle\Model\Product;

use Magento\Bundle\Api\Data\LinkInterfaceFactory;
use Magento\Bundle\Api\Data\OptionInterfaceFactory;
use Magento\Catalog\Api\Data\ProductCustomOptionInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogInventory\Model\Configuration;
use Magento\CatalogRule\Model\RuleFactory;
use Magento\Framework\App\Config\MutableScopeConfigInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Store\Model\ScopeInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Abstract class for testing bundle prices
 * @codingStandardsIgnoreStart
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class BundlePriceAbstract extends TestCase
{
    /** Fixed price type for product custom option */
    const CUSTOM_OPTION_PRICE_TYPE_FIXED = 'fixed';

    /** Percent price type for product custom option */
    const CUSTOM_OPTION_PRICE_TYPE_PERCENT = 'percent';

    /** @var Bootstrap */
    protected $objectManager;

    /** @var ProductRepositoryInterface */
    protected $productRepository;

    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var RuleFactory
     */
    private $ruleFactory;

    /**
     * Get test cases
     * @return array
     */
    abstract public function getTestCases();

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->productRepository = $this->objectManager->create(ProductRepositoryInterface::class);
        $this->productCollectionFactory =
            $this->objectManager->create(CollectionFactory::class);

        $scopeConfig = $this->objectManager->get(MutableScopeConfigInterface::class);
        $scopeConfig->setValue(
            Configuration::XML_PATH_SHOW_OUT_OF_STOCK,
            true,
            ScopeInterface::SCOPE_STORE
        );
        $this->ruleFactory = $this->objectManager->get(RuleFactory::class);
    }

    /**
     * @param array $strategyModifiers
     * @param string $productSku
     * @return void
     * @throws NoSuchEntityException
     * @throws InputException
     * @throws StateException
     * @throws CouldNotSaveException
     */
    protected function prepareFixture($strategyModifiers, $productSku)
    {
        $this->ruleFactory->create()->clearPriceRulesData();

        $bundleProduct = $this->productRepository->get($productSku);

        foreach ($strategyModifiers as $modifier) {
            if (method_exists($this, $modifier['modifierName'])) {
                array_unshift($modifier['data'], $bundleProduct);
                $bundleProduct = call_user_func_array([$this, $modifier['modifierName']], $modifier['data']);
            } else {
                throw new InputException(
                    __('Modifier %s does not exists', $modifier['modifierName'])
                );
            }
        }
        $this->productRepository->save($bundleProduct);
    }

    /**
     * Add simple product to bundle
     *
     * @param Product $bundleProduct
     * @param array $optionsData
     * @return Product
     */
    protected function addSimpleProduct(Product $bundleProduct, array $optionsData)
    {
        $options = [];

        foreach ($optionsData as $optionData) {
            $links = [];
            $linksData = $optionData['links'];
            unset($optionData['links']);

            $option = $this->objectManager->create(OptionInterfaceFactory::class)
                ->create(['data' => $optionData])
                ->setSku($bundleProduct->getSku());

            foreach ($linksData as $linkData) {
                $links[] = $this->objectManager->create(LinkInterfaceFactory::class)
                    ->create(['data' => $linkData]);
            }

            $option->setProductLinks($links);
            $options[] = $option;
        }

        $extension = $bundleProduct->getExtensionAttributes();
        $extension->setBundleProductOptions($options);
        $bundleProduct->setExtensionAttributes($extension);

        return $bundleProduct;
    }

    /**
     * @param Product $bundleProduct
     * @param array $optionsData
     * @return Product
     */
    protected function addCustomOption(Product $bundleProduct, array $optionsData)
    {
        /** @var ProductCustomOptionInterfaceFactory $customOptionFactory */
        $customOptionFactory = $this->objectManager
            ->create(ProductCustomOptionInterfaceFactory::class);

        $options = [];
        foreach ($optionsData as $optionData) {
            $customOption = $customOptionFactory->create(
                [
                    'data' => $optionData
                ]
            );
            $customOption->setProductSku($bundleProduct->getSku());
            $customOption->setOptionId(null);

            $options[] = $customOption;
        }

        $bundleProduct->setOptions($options);
        $bundleProduct->setCanSaveCustomOptions(true);

        return $bundleProduct;
    }
}
// @codingStandardsIgnoreEnd
