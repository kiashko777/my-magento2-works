<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\ObjectManager\TestAsset;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class ConstructorTenArguments extends ConstructorNineArguments
{
    /**
     * @var Basic
     */
    protected $_ten;

    /**
     * Ten arguments
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
     * @param Basic $ten
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
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
        Basic $nine,
        Basic $ten
    )
    {
        parent::__construct($one, $two, $three, $four, $five, $six, $seven, $eight, $nine);
        $this->_ten = $ten;
    }
}
