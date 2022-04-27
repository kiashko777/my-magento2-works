<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Catalog\Model\Attribute\Backend;

use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Attribute\Exception;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test for Start Date attribute backend model
 *
 * @see \Magento\Catalog\Model\Attribute\Backend\Startdate
 *
 * @magentoAppArea Adminhtml
 */
class StartdateTest extends TestCase
{
    /** @var ObjectManagerInterface */
    private $objectManager;

    /** @var ProductInterfaceFactory */
    private $productFactory;

    /** @var Startdate */
    private $startDate;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->objectManager = Bootstrap::getObjectManager();
        $this->productFactory = $this->objectManager->get(ProductInterfaceFactory::class);
        $this->startDate = $this->objectManager->get(Startdate::class);
        $attribute = $this->objectManager->get(Config::class)->getAttribute(Product::ENTITY, 'news_from_date');
        $attribute->setMaxValue(new \DateTime('-10 days'));
        $this->startDate->setAttribute($attribute);
    }

    /**
     * @return void
     */
    public function testBeforeSave(): void
    {
        $product = $this->productFactory->create();
        $product->setNewsFromDate(false);
        $this->startDate->beforeSave($product);
        $this->assertNull($product->getNewsFromDateIsFormated());
    }

    /**
     * @return void
     */
    public function testValidate(): void
    {
        $product = $this->productFactory->create();
        $product->setNewsFromDate(new \DateTime());
        $this->expectException(Exception::class);
        $msg = __('Make sure the To Date is later than or the same as the From Date.');
        $this->expectExceptionMessage((string)$msg);
        $this->startDate->validate($product);
    }
}
