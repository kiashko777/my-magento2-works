<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Tests, that perform search of words, that signal of obsolete code
 */

namespace Magento\Test\Legacy;

use Magento\Framework\App\Utility\AggregateInvoker;
use Magento\Framework\App\Utility\Files;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\TestFramework\Inspection\WordsFinder;
use PHPUnit\Framework\TestCase;

class WordsTest extends TestCase
{
    /**
     * @var WordsFinder
     */
    protected static $_wordsFinder;

    public static function setUpBeforeClass(): void
    {
        self::$_wordsFinder = new WordsFinder(
            glob(__DIR__ . '/_files/words_*.xml'),
            BP,
            new ComponentRegistrar()
        );
    }

    public function testWords()
    {
        $invoker = new AggregateInvoker($this);
        $invoker(
        /**
         * @param string $file
         */
            function ($file) {
                $words = self::$_wordsFinder->findWords(realpath($file));
                if ($words) {
                    $this->fail("Found words: '" . implode("', '", $words) . "' in '{$file}' file");
                }
            },
            Files::init()->getAllFiles()
        );
    }
}
