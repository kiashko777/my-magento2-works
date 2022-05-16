<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\View;

use Magento\Framework\Data\Collection;
use Magento\Framework\View\Layout\Argument\UpdaterInterface;

/**
 * Dummy layout argument updater model
 */
class LayoutArgumentObjectUpdater implements UpdaterInterface
{
    /**
     * Update specified argument
     *
     * @param Collection $argument
     * @return Collection
     */
    public function update($argument)
    {
        $calls = $argument->getUpdaterCall();
        $calls[] = 'updater call';
        $argument->setUpdaterCall($calls);
        return $argument;
    }
}
