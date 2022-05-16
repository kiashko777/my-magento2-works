<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\I18n\Dictionary\Loader;

use InvalidArgumentException;
use Magento\Setup\Module\I18n\Dictionary;

/**
 * Dictionary loader interface
 */
interface FileInterface
{
    /**
     * Load dictionary
     *
     * @param string $file
     * @return Dictionary
     * @throws InvalidArgumentException
     */
    public function load($file);
}
