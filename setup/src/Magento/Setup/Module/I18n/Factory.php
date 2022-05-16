<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\I18n;

use InvalidArgumentException;
use Magento\Setup\Module\I18n\Dictionary\Phrase;
use Magento\Setup\Module\I18n\Dictionary\WriterInterface;
use const PATHINFO_EXTENSION;

/**
 *  Abstract Factory
 */
class Factory
{
    /**
     * Create dictionary writer
     *
     * @param string $filename
     * @return WriterInterface
     * @throws InvalidArgumentException
     */
    public function createDictionaryWriter($filename = null)
    {
        if (!$filename) {
            $writer = new Dictionary\Writer\Csv\Stdo();
        } else {
            switch (pathinfo($filename, PATHINFO_EXTENSION)) {
                case 'csv':
                default:
                    $writer = new Dictionary\Writer\Csv($filename);
                    break;
            }
        }
        return $writer;
    }

    /**
     * Create locale
     *
     * @param string $locale
     * @return Locale
     */
    public function createLocale($locale)
    {
        return new Locale($locale);
    }

    /**
     * Create dictionary
     *
     * @return Dictionary
     */
    public function createDictionary()
    {
        return new Dictionary();
    }

    /**
     * Create Phrase
     *
     * @param array $data
     * @return Phrase
     */
    public function createPhrase(array $data)
    {
        return new Dictionary\Phrase(
            $data['phrase'],
            $data['translation'],
            isset($data['context_type']) ? $data['context_type'] : null,
            isset($data['context_value']) ? $data['context_value'] : null,
            isset($data['quote']) ? $data['quote'] : null
        );
    }
}
