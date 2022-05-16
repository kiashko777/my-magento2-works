<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Model\Product\Attribute\Repository;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_attribute.php');
/** @var $attributeRepository Repository */
$attributeRepository = Bootstrap::getObjectManager()
    ->get(Repository::class);
/** @var $attribute AttributeInterface */
$attribute = $attributeRepository->get('test_attribute_code_333');

$attributeRepository->save($attribute->setIsUserDefined(0));
