<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Model;

use Magento\Framework\Config\ConfigOptionsListConstants;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;

/**
 * Generates a crypt.
 */
class CryptKeyGenerator implements CryptKeyGeneratorInterface
{
    /**
     * @var Random
     */
    private $random;

    /**
     * CryptKeyGenerator constructor.
     *
     * @param Random $random
     */
    public function __construct(Random $random)
    {
        $this->random = $random;
    }

    /**
     * Generates & returns a string to be used as crypt key.
     *
     * @return string
     * @throws LocalizedException
     */
    public function generate()
    {
        // md5() here is not for cryptographic use. It used for generate encryption key itself
        // and do not encrypt any passwords
        // phpcs:ignore Magento2.Security.InsecureFunction
        return md5($this->getRandomString());
    }

    /**
     * Returns a random string.
     *
     * @return string
     * @throws LocalizedException
     */
    private function getRandomString()
    {
        return $this->random->getRandomString(ConfigOptionsListConstants::STORE_KEY_RANDOM_STRING_SIZE);
    }
}
