<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Fixtures\FixturesAsserts;

use AssertionError;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ConfigurableProduct\Api\OptionRepositoryInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class ConfigurableProductsAssert
 *
 * Class performs assertion that generated configurable products are valid
 * after running setup:performance:generate-fixtures command
 */
class ConfigurableProductsAssert
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var OptionRepositoryInterface
     */
    private $optionRepository;

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
     * Asserts that generated configurable products are valid
     *
     * @return bool
     * @throws NoSuchEntityException
     * @throws InputException
     * @throws AssertionError
     */
    public function assert()
    {
        $productsMap = [
            'Configurable Products - Default %s' => [
                'attributes' => 1,
                'options' => 3,
                'amount' => 2,
            ],
            'Configurable Products - Color-Size %s' => [
                'attributes' => 2,
                'options' => 3,
                'amount' => 2,
            ],
            'Configurable Products 2-2 %s' => [
                'attributes' => 2,
                'options' => 2,
                'amount' => 2,
            ],
        ];

        foreach ($productsMap as $skuPattern => $expectedData) {
            $configurableSku = sprintf($skuPattern, 1);
            $product = $this->productRepository->get($configurableSku);
            $this->productAssert->assertProductsCount($skuPattern, $expectedData['amount']);
            $this->productAssert->assertProductType('configurable', $product);
            $options = $this->optionRepository->getList($configurableSku);

            if ($expectedData['attributes'] !== count($options)) {
                throw new AssertionError('Configurable options amount is wrong');
            }

            if ($expectedData['options'] !== count($options[0]->getValues())) {
                throw new AssertionError('Configurable option values amount is wrong');
            }
        }

        return true;
    }
}
