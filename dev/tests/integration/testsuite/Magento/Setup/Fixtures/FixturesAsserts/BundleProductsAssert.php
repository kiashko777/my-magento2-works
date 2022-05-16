<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Fixtures\FixturesAsserts;

use AssertionError;
use Magento\Bundle\Model\Product\OptionList;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Setup\Fixtures\BundleProductsFixture;

/**
 * Class BundleProductsAssert
 *
 * Class performs assertion that generated bundle products are valid
 * after running setup:performance:generate-fixtures command
 */
class BundleProductsAssert
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var OptionList
     */
    private $optionList;

    /**
     * @var ProductAssert
     */
    private $productAssert;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param OptionList $optionList
     * @param ProductAssert $productAssert
     */
    public function __construct(
        ProductRepositoryInterface       $productRepository,
        OptionList              $optionList,
        ProductAssert $productAssert
    )
    {
        $this->productRepository = $productRepository;
        $this->optionList = $optionList;
        $this->productAssert = $productAssert;
    }

    /**
     * Asserts that generated bundled products are valid
     *
     * @return bool
     * @throws NoSuchEntityException
     * @throws AssertionError
     */
    public function assert()
    {
        $bundleSkuSuffix = '2-2';
        $product = $this->productRepository->get(
            sprintf(BundleProductsFixture::SKU_PATTERN, 1, $bundleSkuSuffix)
        );

        $this->productAssert->assertProductsCount(
            sprintf(BundleProductsFixture::SKU_PATTERN, '%s', $bundleSkuSuffix),
            2
        );
        $this->productAssert->assertProductType('bundle', $product);

        if (2 !== count($this->optionList->getItems($product))) {
            throw new AssertionError('Bundle options amount is wrong');
        }

        foreach ($this->optionList->getItems($product) as $option) {
            if (2 !== count($option->getProductLinks())) {
                throw new AssertionError('Bundle option product links amount is wrong');
            }
        }

        return true;
    }
}
