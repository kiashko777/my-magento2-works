<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Model\Product\Attribute\Repository;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

/** @var $attributeRepository Repository */
$attributeRepository = Bootstrap::getObjectManager()
    ->get(Repository::class);

try {
    /** @var $attribute AttributeInterface */
    $attribute = $attributeRepository->get('test_attribute_code_333');
    $attributeRepository->save($attribute->setIsUserDefined(1));
    // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedCatch
} catch (NoSuchEntityException $e) {
}
/** @var Registry $registry */
$registry = Bootstrap::getObjectManager()->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

try {
    $attribute = $attributeRepository->get('test_attribute_code_333');
    if ($attribute->getId()) {
        $attribute->delete();
    }
    // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedCatch
} catch (Exception $e) {
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
