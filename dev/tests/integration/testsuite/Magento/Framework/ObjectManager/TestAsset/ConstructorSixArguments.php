<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\ObjectManager\TestAsset;

class ConstructorSixArguments extends ConstructorFiveArguments
{
    /**
     * @var Basic
     */
    protected $_six;

    /**
     * Six arguments
     *
     * @param Basic $one
     * @param Basic $two
     * @param Basic $three
     * @param Basic $four
     * @param Basic $five
     * @param Basic $six
     */
    public function __construct(
        Basic $one,
        Basic $two,
        Basic $three,
        Basic $four,
        Basic $five,
        Basic $six
    )
    {
        parent::__construct($one, $two, $three, $four, $five);
        $this->_six = $six;
    }
}
