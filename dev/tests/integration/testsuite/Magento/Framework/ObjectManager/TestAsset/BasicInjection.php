<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\ObjectManager\TestAsset;

class BasicInjection
{
    /**
     * @var Basic
     */
    protected $_object;

    /**
     * @param Basic $object
     */
    public function __construct(Basic $object)
    {
        $this->_object = $object;
    }

    public function getBasicDependency()
    {
        return $this->_object;
    }
}
