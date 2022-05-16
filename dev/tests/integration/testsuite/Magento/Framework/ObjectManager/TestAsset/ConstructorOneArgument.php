<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\ObjectManager\TestAsset;

class ConstructorOneArgument
{
    /**
     * @var Basic
     */
    protected $_one;

    /**
     * One argument
     *
     * @param Basic $one
     */
    public function __construct(Basic $one)
    {
        $this->_one = $one;
    }

    /**
     * @return Basic
     */
    public function getBasicDependency()
    {
        return $this->_one;
    }
}
