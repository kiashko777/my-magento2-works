<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Fixtures\FixturesAsserts;

use AssertionError;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ConfigurableProduct\Api\OptionRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Setup\Fixtures\SimpleProductsFixture;

/**
 * Class SimpleProductsAssert
 *
 * Class performs assertion that generated simple products are valid
 * after running setup:performance:generate-fixtures command
 */
class SimpleProductsAssert
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var ProductAssert
     */
    private $productAssert;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param OptionRepositoryInterface $optionRepository
     * @param ProductAssert $productAssert
     */
    public function __construct(
        ProductRepositoryInterface            $productRepository,
        OptionRepositoryInterface $optionRepository,
        ProductAssert $productAssert
    )
    {
        $this->productRepository = $productRepository;
        $this->optionRepository = $optionRepository;
        $this->productAssert = $productAssert;
    }

    /**
     * Asserts that generated simple products are valid
     *
     * @return bool
     * @throws NoSuchEntityException
     * @throws AssertionError
     */
    public function assert()
    {
        $product = $this->productRepository->get(sprintf(SimpleProductsFixture::SKU_PATTERN, 1));
        $this->productAssert->assertProductsCount(SimpleProductsFixture::SKU_PATTERN, 2);
        $this->productAssert->assertProductType('simple', $product);

        return true;
    }
}
