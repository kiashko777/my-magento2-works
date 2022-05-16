<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\Dependency\Parser\Config;

use PHPUnit\Framework\TestCase;

class XmlTest extends TestCase
{
    /**
     * @var string
     */
    protected $fixtureDir;

    /**
     * @var Xml
     */
    protected $parser;

    public function testParse()
    {
        $expected = [
            'Magento\Module1',
            'Magento\Module2',
        ];

        $actual = $this->parser->parse(
            ['files_for_parse' => [$this->fixtureDir . 'module1.xml', $this->fixtureDir . 'module2.xml']]
        );

        $this->assertEquals($expected, $actual);
    }

    protected function setUp(): void
    {
        $this->fixtureDir = realpath(__DIR__ . '/../../_files') . '/';

        $this->parser = new Xml();
    }
}
