<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Tests to ensure that all license blocks are represented by placeholders
 */

namespace Magento\Test\Legacy;

use Magento\Framework\App\Utility\AggregateInvoker;
use Magento\Framework\App\Utility\Files;
use PHPUnit\Framework\TestCase;

class LicenseTest extends TestCase
{
    public function testLegacyComment()
    {
        $invoker = new AggregateInvoker($this);
        $invoker(
            function ($filename) {
                $fileText = file_get_contents($filename);
                if (!preg_match_all('#/\*\*.+@copyright.+?\*/#s', $fileText, $matches)) {
                    return;
                }

                foreach ($matches[0] as $commentText) {
                    foreach (['Irubin Consulting Inc', 'DBA Varien', 'Magento Inc'] as $legacyText) {
                        $this->assertStringNotContainsString(
                            $legacyText,
                            $commentText,
                            "The license of file {$filename} contains legacy text."
                        );
                    }
                }
            },
            $this->legacyCommentDataProvider()
        );
    }

    public function legacyCommentDataProvider()
    {
        $allFiles = Files::init()->getAllFiles();
        $result = [];
        foreach ($allFiles as $file) {
            if (!file_exists($file[0]) || !is_readable($file[0])) {
                continue;
            }
            $result[] = [$file[0]];
        }
        return $result;
    }
}
