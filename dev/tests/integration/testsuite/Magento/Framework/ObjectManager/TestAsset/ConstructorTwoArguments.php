<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\ObjectManager\TestAsset;

class ConstructorTwoArguments extends ConstructorOneArgument
{
    /**
     * @var Basic
     */
    protected $_two;

    /**
     * Two arguments
     *
     * @param Basic $one
     * @param Basic $two
     */
    public function __construct(
        Basic $one,
        Basic $two
    )
    {
        parent::__construct($one);
        $this->_two = $two;
    }
}
