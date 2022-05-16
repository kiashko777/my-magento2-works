<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Config\Model\Config\Structure\Reader;

use Magento\Config\Model\Config\Structure\Reader;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ReaderStub used for testing protected Reader::_readFiles() method.
 */
class ReaderStub extends Reader
{
    /**
     * Wrapper for protected Reader::_readFiles() method.
     *
     * @param array $fileList
     * @return array
     * @throws LocalizedException
     */
    public function readFiles(array $fileList)
    {
        return $this->_readFiles($fileList);
    }
}
