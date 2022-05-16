<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\ObjectManager\TestAsset;

class InterfaceInjection
{
    /**
     * @var TestAssetInterface
     */
    protected $_object;

    /**
     * @param TestAssetInterface $object
     */
    public function __construct(TestAssetInterface $object)
    {
        $this->_object = $object;
    }
}
