<?php
/***
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Version\Controller\Index;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractController;

class IndexTest extends AbstractController
{
    public function testIndexAction()
    {
        // Execute controller to get version response
        $this->dispatch('magento_version/index/index');
        $body = $this->getResponse()->getBody();

        $objectManager = Bootstrap::getObjectManager();
        /** @var ProductMetadataInterface $productMetadata */
        $productMetadata = $objectManager->get(ProductMetadataInterface::class);
        $name = $productMetadata->getName();
        $edition = $productMetadata->getEdition();

        $fullVersion = $productMetadata->getVersion();
        if ($this->isComposerBasedInstallation($fullVersion)) {
            $versionParts = explode('.', $fullVersion);
            $majorMinor = $versionParts[0] . '.' . $versionParts[1];

            // Response must contain Major.Minor version, product name, and edition
            $this->assertStringContainsString($majorMinor, $body);
            $this->assertStringContainsString($name, $body);
            $this->assertStringContainsString($edition, $body);

            // Response must not contain full version including patch version
            $this->assertStringNotContainsString($fullVersion, $body);
        } else {
            // Response is supposed to be empty when the project is installed from git
            $this->assertEmpty($body);
        }
    }

    private function isComposerBasedInstallation($fullVersion)
    {
        $versionParts = explode('-', $fullVersion);
        return !(isset($versionParts[0]) && $versionParts[0] == 'dev');
    }
}
