<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaStorage\Model\File\Storage;

use Magento\Framework\App\Response\Http;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Tests for \Magento\MediaStorage\Model\File\Storage\Response class
 */
class ResponseTest extends TestCase
{
    /**
     * test for \Magento\MediaStorage\Model\File\Storage\Response::sendResponse()
     *
     * @return void
     */
    public function testSendResponse(): void
    {
        $expectedHeaders = [
            [
                'field_name' => 'X-Content-Type-Options',
                'field_value' => 'nosniff',
            ],
            [
                'field_name' => 'X-XSS-Protection',
                'field_value' => '1; mode=block',
            ],
            [
                'field_name' => 'X-Frame-Options',
                'field_value' => 'SAMEORIGIN',
            ],
        ];
        $filePath = realpath(__DIR__ . '/../../../_files/test_file.html');
        /** @var Response $response */
        $mediaStorageResponse = Bootstrap::getObjectManager()->create(
            Response::class
        );
        $mediaStorageResponse->setFilePath($filePath);
        ob_start();
        $mediaStorageResponse->sendResponse();
        ob_end_clean();
        /** @var Http $frameworkResponse */
        $frameworkResponse = Bootstrap::getObjectManager()->get(
            \Magento\Framework\HTTP\PhpEnvironment\Response::class
        );
        $actualHeaders = [];
        foreach ($frameworkResponse->getHeaders() as $responseHeader) {
            $actualHeaders[] = [
                'field_name' => $responseHeader->getFieldName(),
                'field_value' => $responseHeader->getFieldValue(),
            ];
        }
        foreach ($expectedHeaders as $expected) {
            $this->assertTrue(in_array($expected, $actualHeaders));
        }
    }
}
