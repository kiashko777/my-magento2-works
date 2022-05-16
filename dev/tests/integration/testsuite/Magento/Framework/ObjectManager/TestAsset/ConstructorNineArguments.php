<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\ObjectManager\TestAsset;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class ConstructorNineArguments extends ConstructorEightArguments
{
    /**
     * @var Basic
     */
    protected $_nine;

    /**
     * Nine arguments
     *
     * @param Basic $one
     * @param Basic $two
     * @param Basic $three
     * @param Basic $four
     * @param Basic $five
     * @param Basic $six
     * @param Basic $seven
     * @param Basic $eight
     * @param Basic $nine
     */
    public function __construct(
        Basic $one,
        Basic $two,
        Basic $three,
        Basic $four,
        Basic $five,
        Basic $six,
        Basic $seven,
        Basic $eight,
        Basic $nine
    )
    {
        parent::__construct($one, $two, $three, $four, $five, $six, $seven, $eight);
        $this->_nine = $nine;
    }
}
