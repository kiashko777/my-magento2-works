<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SomeModule\Api\Data;

use Magento\Eav\Api\Data\AttributeExtensionInterface;
use Magento\Framework\Api\CustomAttributesDataInterface;

interface SomeInterface extends CustomAttributesDataInterface
{
    /**
     * @return AttributeExtensionInterface|null
     */
    public function getExtensionAttributes();
}
