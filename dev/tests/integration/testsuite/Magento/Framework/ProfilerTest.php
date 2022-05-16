<?php
/**
 * Test case for \Magento\Framework\Profiler
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework;

use Magento\Framework\Profiler\Driver\Standard;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ProfilerTest extends TestCase
{
    /**
     * @dataProvider applyConfigDataProvider
     * @param array $config
     * @param array $expectedDrivers
     */
    public function testApplyConfigWithDrivers(array $config, array $expectedDrivers)
    {
        $profiler = new Profiler();
        $profiler::applyConfig($config, '');
        $this->assertClassHasAttribute('_drivers', Profiler::class);
        $object = new ReflectionClass(Profiler::class);
        $attribute = $object->getProperty('_drivers');
        $attribute->setAccessible(true);
        $propertyObject = $attribute->getValue($profiler);
        $attribute->setAccessible(false);
        $this->assertEquals($expectedDrivers, $propertyObject);
    }

    /**
     * @return array
     */
    public function applyConfigDataProvider()
    {
        return [
            'Empty config does not create any driver' => ['config' => [], 'drivers' => []],
            'Integer 0 does not create any driver' => [
                'config' => ['drivers' => [0]],
                'drivers' => [],
            ],
            'Integer 1 does creates standard driver' => [
                'config' => ['drivers' => [1]],
                'drivers' => [new Standard()],
            ],
            'Config array key sets driver type' => [
                'configs' => ['drivers' => ['standard' => 1]],
                'drivers' => [new Standard()],
            ],
            'Config array key ignored when type set' => [
                'config' => ['drivers' => ['custom' => ['type' => 'standard']]],
                'drivers' => [new Standard()],
            ],
            'Config with outputs element as integer 1 creates output' => [
                'config' => [
                    'drivers' => [['outputs' => ['html' => 1]]],
                    'baseDir' => '/some/base/dir',
                ],
                'drivers' => [
                    new Standard(
                        ['outputs' => [['type' => 'html', 'baseDir' => '/some/base/dir']]]
                    ),
                ],
            ],
            'Config with outputs element as integer 0 does not create output' => [
                'config' => ['drivers' => [['outputs' => ['html' => 0]]]],
                'drivers' => [new Standard()],
            ],
            'Config with shortly defined outputs element' => [
                'config' => ['drivers' => [['outputs' => ['foo' => 'html']]]],
                'drivers' => [
                    new Standard(['outputs' => [['type' => 'html']]]),
                ],
            ],
            'Config with fully defined outputs element options' => [
                'config' => [
                    'drivers' => [
                        [
                            'outputs' => [
                                'foo' => [
                                    'type' => 'html',
                                    'filterName' => '/someFilter/',
                                    'thresholds' => ['someKey' => 123],
                                    'baseDir' => '/custom/dir',
                                ],
                            ],
                        ],
                    ],
                ],
                'drivers' => [
                    new Standard(
                        [
                            'outputs' => [
                                [
                                    'type' => 'html',
                                    'filterName' => '/someFilter/',
                                    'thresholds' => ['someKey' => 123],
                                    'baseDir' => '/custom/dir',
                                ],
                            ],
                        ]
                    ),
                ],
            ],
            'Config with shortly defined output' => [
                'config' => ['drivers' => [['output' => 'html']]],
                'drivers' => [
                    new Standard(['outputs' => [['type' => 'html']]]),
                ],
            ]
        ];
    }

    protected function tearDown(): void
    {
        Profiler::reset();
    }
}
