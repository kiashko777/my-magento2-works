<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Legacy tests to find obsolete acl declaration
 */

namespace Magento\Test\Legacy;

use Magento\Framework\App\Utility\AggregateInvoker;
use Magento\Framework\App\Utility\Files;
use PHPUnit\Framework\TestCase;

class ObsoleteAclTest extends TestCase
{
    public function testAclDeclarations()
    {
        $invoker = new AggregateInvoker($this);
        $invoker(
        /**
         * @param string $aclFile
         */
            function ($aclFile) {
                $aclXml = simplexml_load_file($aclFile);
                $xpath = '/config/acl/*[boolean(./children) or boolean(./title)]';
                $this->assertEmpty(
                    $aclXml->xpath($xpath),
                    'Obsolete acl structure detected in file ' . $aclFile . '.'
                );
            },
            Files::init()->getMainConfigFiles()
        );
    }
}
