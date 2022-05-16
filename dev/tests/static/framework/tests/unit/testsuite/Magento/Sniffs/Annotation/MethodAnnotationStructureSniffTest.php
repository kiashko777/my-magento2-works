<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Sniffs\Annotation;

use Magento\TestFramework\CodingStandard\Tool\CodeSniffer;
use Magento\TestFramework\CodingStandard\Tool\CodeSniffer\Wrapper;
use PHPUnit\Framework\TestCase;

class MethodAnnotationStructureSniffTest extends TestCase
{
    /**
     * @return array
     */
    public function processDataProvider()
    {
        return [
            [
                'MethodAnnotationFixture.php',
                'method_annotation_errors.txt'
            ]
        ];
    }

    /**
     * @param string $fileUnderTest
     * @param string $expectedReportFile
     * @dataProvider processDataProvider
     */
    public function testProcess($fileUnderTest, $expectedReportFile)
    {
        $reportFile = __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'phpcs_report.txt';
        $this->copyFile(
            __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR,
            TESTS_TEMP_DIR . DIRECTORY_SEPARATOR
        );
        $codeSniffer = new CodeSniffer(
            'Magento',
            $reportFile,
            new Wrapper()
        );
        $result = $codeSniffer->run(
            [TESTS_TEMP_DIR . DIRECTORY_SEPARATOR . $fileUnderTest]
        );
        $actual = file_get_contents($reportFile);
        $expected = file_get_contents(
            TESTS_TEMP_DIR . DIRECTORY_SEPARATOR . $expectedReportFile
        );
        unlink($reportFile);
        $this->assertEquals(2, $result);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Copy a file
     *
     * @param string $source
     * @param string $destination
     */
    private function copyFile($source, $destination): void
    {
        $sourcePath = $source;
        $destinationPath = $destination;
        $sourceDirectory = opendir($sourcePath);
        while ($readFile = readdir($sourceDirectory)) {
            if ($readFile != '.' && $readFile != '..') {
                if (!file_exists($destinationPath . $readFile)) {
                    copy($sourcePath . $readFile, $destinationPath . $readFile);
                }
            }
        }
        closedir($sourceDirectory);
    }
}
