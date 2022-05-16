<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\ObjectManager\TestAsset;

class ConstructorFiveArguments extends ConstructorFourArguments
{
    /**
     * @var Basic
     */
    protected $_five;

    /**
     * Five arguments
     *
     * @param Basic $one
     * @param Basic $two
     * @param Basic $three
     * @param Basic $four
     * @param Basic $five
     */
    public function __construct(
        Basic $one,
        Basic $two,
        Basic $three,
        Basic $four,
        Basic $five
    )
    {
        parent::__construct($one, $two, $three, $four);
        $this->_five = $five;
    }
}
