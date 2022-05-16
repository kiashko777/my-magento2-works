<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\ObjectManager\TestAsset;

class ConstructorFourArguments extends ConstructorThreeArguments
{
    /**
     * @var Basic
     */
    protected $_four;

    /**
     * Four arguments
     *
     * @param Basic $one
     * @param Basic $two
     * @param Basic $three
     * @param Basic $four
     */
    public function __construct(
        Basic $one,
        Basic $two,
        Basic $three,
        Basic $four
    )
    {
        parent::__construct($one, $two, $three);
        $this->_four = $four;
    }
}
