<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\ObjectManager\TestAsset;

class ConstructorThreeArguments extends ConstructorTwoArguments
{
    /**
     * @var Basic
     */
    protected $_three;

    /**
     * Three arguments
     *
     * @param Basic $one
     * @param Basic $two
     * @param Basic $three
     */
    public function __construct(
        Basic $one,
        Basic $two,
        Basic $three
    )
    {
        parent::__construct($one, $two);
        $this->_three = $three;
    }
}
