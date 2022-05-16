<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\ObjectManager\TestAsset;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class ConstructorEightArguments extends ConstructorSevenArguments
{
    /**
     * @var Basic
     */
    protected $_eight;

    /**
     * Eight arguments
     *
     * @param Basic $one
     * @param Basic $two
     * @param Basic $three
     * @param Basic $four
     * @param Basic $five
     * @param Basic $six
     * @param Basic $seven
     * @param Basic $eight
     */
    public function __construct(
        Basic $one,
        Basic $two,
        Basic $three,
        Basic $four,
        Basic $five,
        Basic $six,
        Basic $seven,
        Basic $eight
    )
    {
        parent::__construct($one, $two, $three, $four, $five, $six, $seven);
        $this->_eight = $eight;
    }
}
