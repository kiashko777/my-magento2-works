<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Tests for obsolete directives in email templates
 */

namespace Magento\Test\Legacy;

use Magento\Framework\App\Utility\AggregateInvoker;
use Magento\Framework\App\Utility\Files;
use PHPUnit\Framework\TestCase;

class EmailTemplateTest extends TestCase
{
    public function testObsoleteDirectives()
    {
        $invoker = new AggregateInvoker($this);
        $invoker(
        /**
         * @param string $file
         */
            function ($file) {
                $this->assertDoesNotMatchRegularExpression(
                    '/\{\{htmlescape.*?\}\}/i',
                    file_get_contents($file),
                    'Directive {{htmlescape}} is obsolete. Use {{var}} instead.'
                );

                $this->assertDoesNotMatchRegularExpression(
                    '/\{\{escapehtml.*?\}\}/i',
                    file_get_contents($file),
                    'Directive {{escapehtml}} is obsolete. Use {{var}} instead.'
                );
            },
            Files::init()->getEmailTemplates()
        );
    }
}
