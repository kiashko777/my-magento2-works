<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test\Legacy\Magento\Framework\ObjectManager;

use Magento\Framework\App\Utility\AggregateInvoker;
use Magento\Framework\App\Utility\Files;
use Magento\Framework\Console\CommandList;
use PHPUnit\Framework\TestCase;

class DiConfigTest extends TestCase
{
    public function testObsoleteDiFormat()
    {
        $invoker = new AggregateInvoker($this);
        $invoker(
            [$this, 'assertObsoleteFormat'],
            Files::init()->getDiConfigs(true)
        );
    }

    /**
     * Scan the specified di.xml file and assert that it has no obsolete nodes
     *
     * @param string $file
     */
    public function assertObsoleteFormat($file)
    {
        $xml = simplexml_load_file($file);
        $this->assertSame(
            [],
            $xml->xpath('//param'),
            'The <param> node is obsolete. Instead, use the <argument name="..." xsi:type="...">'
        );
        $this->assertSame(
            [],
            $xml->xpath('//instance'),
            'The <instance> node is obsolete. Instead, use the <argument name="..." xsi:type="object">'
        );
        $this->assertSame(
            [],
            $xml->xpath('//array'),
            'The <array> node is obsolete. Instead, use the <argument name="..." xsi:type="array">'
        );
        $this->assertSame(
            [],
            $xml->xpath('//item[@key]'),
            'The <item key="..."> node is obsolete. Instead, use the <item name="..." xsi:type="...">'
        );
        $this->assertSame(
            [],
            $xml->xpath('//value'),
            'The <value> node is obsolete. Instead, provide the actual value as a text literal.'
        );
    }

    public function testCommandListClassIsNotDirectlyConfigured()
    {
        $invoker = new AggregateInvoker($this);
        $invoker(
            [$this, 'assertCommandListClassIsNotDirectlyConfigured'],
            Files::init()->getDiConfigs(true)
        );
    }

    /**
     * Scan the specified di.xml file and assert that it has no directly configured CommandList class
     *
     * @param string $file
     */
    public function assertCommandListClassIsNotDirectlyConfigured($file)
    {
        $xml = simplexml_load_file($file);
        foreach ($xml->xpath('//type') as $type) {
            $this->assertNotContains(
                CommandList::class,
                $type->attributes(),
                'Use \Magento\Framework\Console\CommandListInterface instead of \Magento\Framework\Console\CommandList'
            );
        }
    }
}
