<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestFramework\Bootstrap;

use Magento\Framework\Shell;
use Magento\TestFramework\MemoryLimit;

class MemoryFactory
{
    /**
     * @var Shell
     */
    private $shell;

    /**
     * @param Shell $shell
     */
    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    /**
     * @param string $memUsageLimit
     * @param string $memLeakLimit
     * @return Memory
     */
    public function create($memUsageLimit, $memLeakLimit)
    {
        return new Memory(
            new MemoryLimit(
                $memUsageLimit,
                $memLeakLimit,
                new \Magento\TestFramework\Helper\Memory($this->shell)
            )
        );
    }
}
