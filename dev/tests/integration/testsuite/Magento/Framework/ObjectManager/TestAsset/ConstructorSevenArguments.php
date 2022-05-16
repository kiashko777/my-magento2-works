<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\ObjectManager\TestAsset;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class ConstructorSevenArguments extends ConstructorSixArguments
{
    /**
     * @var Basic
     */
    protected $_seven;

    /**
     * Seven arguments
     *
     * @param Basic $one
     * @param Basic $two
     * @param Basic $three
     * @param Basic $four
     * @param Basic $five
     * @param Basic $six
     * @param Basic $seven
     */
    public function __construct(
        Basic $one,
        Basic $two,
        Basic $three,
        Basic $four,
        Basic $five,
        Basic $six,
        Basic $seven
    )
    {
        parent::__construct($one, $two, $three, $four, $five, $six);
        $this->_seven = $seven;
    }
}
