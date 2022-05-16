<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Widget\Model\Template;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class FilterTest extends TestCase
{
    public function testMediaDirective()
    {
        $image = 'wysiwyg/VB.png';
        $construction = ['{{media url="' . $image . '"}}', 'media', ' url="' . $image . '"'];
        $baseUrl = 'http://localhost/media/';

        /** @var Filter $filter */
        $filter = Bootstrap::getObjectManager()->create(
            Filter::class
        );
        $result = $filter->mediaDirective($construction);
        $this->assertEquals($baseUrl . $image, $result);
    }

    public function testMediaDirectiveWithEncodedQuotes()
    {
        $image = 'wysiwyg/VB.png';
        $construction = ['{{media url=&quot;' . $image . '&quot;}}', 'media', ' url=&quot;' . $image . '&quot;'];
        $baseUrl = 'http://localhost/media/';

        /** @var Filter $filter */
        $filter = Bootstrap::getObjectManager()->create(
            Filter::class
        );
        $result = $filter->mediaDirective($construction);
        $this->assertEquals($baseUrl . $image, $result);
    }
}
