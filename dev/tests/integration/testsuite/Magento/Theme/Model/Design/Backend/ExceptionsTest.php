<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Theme\Model\Design\Backend;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ExceptionsTest extends TestCase
{
    /**
     * @var Exceptions
     */
    private $exceptions = null;

    /** @var Json */
    private $serializer;

    /**
     * Basic test, checks that saved value contains all required entries and is saved as an array
     * @magentoDbIsolation enabled
     */
    public function testSaveValueIsFormedNicely()
    {
        $value = [
            '1' => ['search' => '/Opera/', 'value' => 'Magento/blank'],
            '2' => ['search' => '/Firefox/', 'value' => 'Magento/blank'],
        ];

        $this->exceptions->setValue($value);
        $this->exceptions->save();

        $processedValue = $this->serializer->unserialize($this->exceptions->getValue());
        $this->assertEquals(count($processedValue), 2, 'Number of saved values is wrong');

        $entry = $processedValue['1'];
        $this->assertArrayHasKey('search', $entry);
        $this->assertArrayHasKey('value', $entry);
        $this->assertArrayHasKey('regexp', $entry);
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testSaveEmptyValueIsSkipped()
    {
        $value = [
            '1' => ['search' => '/Opera/', 'value' => 'Magento/blank'],
            '2' => ['search' => '', 'value' => 'Magento/blank'],
            '3' => ['search' => '/Firefox/', 'value' => 'Magento/blank'],
        ];

        $this->exceptions->setValue($value);
        $this->exceptions->save();

        $processedValue = $this->serializer->unserialize($this->exceptions->getValue());
        $emptyIsSkipped = isset($processedValue['1']) && !isset($processedValue['2']) && isset($processedValue['3']);
        $this->assertTrue($emptyIsSkipped);
    }

    /**
     * @param array $designException
     * @param string $regexp
     * @dataProvider saveExceptionDataProvider
     * @magentoDbIsolation enabled
     */
    public function testSaveException($designException, $regexp)
    {
        $this->exceptions->setValue(['1' => $designException]);
        $this->exceptions->save();

        $processedValue = $this->serializer->unserialize($this->exceptions->getValue());
        $this->assertEquals($processedValue['1']['regexp'], $regexp);
    }

    /**
     * @return array
     */
    public function saveExceptionDataProvider()
    {
        $result = [
            [['search' => 'Opera', 'value' => 'Magento/blank'], '/Opera/i'],
            [['search' => '/Opera/', 'value' => 'Magento/blank'], '/Opera/'],
            [['search' => '#iPad|iPhone#i', 'value' => 'Magento/blank'], '#iPad|iPhone#i'],
            [
                ['search' => 'Mozilla (3.6+)/Firefox', 'value' => 'Magento/blank'],
                '/Mozilla \\(3\\.6\\+\\)\\/Firefox/i'
            ],
        ];

        return $result;
    }

    /**
     * @var array $value
     * @dataProvider saveWrongExceptionDataProvider
     * @magentoDbIsolation enabled
     */
    public function testSaveWrongException($value)
    {
        $this->expectException(LocalizedException::class);

        $this->exceptions->setValue($value);
        $this->exceptions->save();
    }

    /**
     * @return array
     */
    public function saveWrongExceptionDataProvider()
    {
        $result = [
            [
                [
                    '1' => ['search' => '/Opera/', 'value' => 'Magento/blank'],
                    '2' => ['search' => '/invalid_regexp(/', 'value' => 'Magento/blank'],
                ],
            ],
            [
                [
                    '1' => ['search' => '/invalid_regexp', 'value' => 'Magento/blank'],
                    '2' => ['search' => '/Opera/', 'value' => 'Magento/blank'],
                ]
            ],
            [
                [
                    '1' => ['search' => 'invalid_regexp/iU', 'value' => 'Magento/blank'],
                    '2' => ['search' => '/Opera/', 'value' => 'Magento/blank'],
                ]
            ],
            [
                [
                    '1' => ['search' => 'invalid_regexp#', 'value' => 'Magento/blank'],
                    '2' => ['search' => '/Opera/', 'value' => 'Magento/blank'],
                ]
            ],
            [
                [
                    '1' => ['search' => '/Firefox/'],
                    '2' => ['search' => '/Opera/', 'value' => 'Magento/blank'],
                ]
            ],
            [
                [
                    '1' => ['value' => 'Magento/blank'],
                    '2' => ['search' => '/Opera/', 'value' => 'Magento/blank'],
                ]
            ],
        ];

        return $result;
    }

    protected function setUp(): void
    {
        $this->exceptions = Bootstrap::getObjectManager()->create(
            Exceptions::class
        );
        $this->exceptions->setScope('default');
        $this->exceptions->setScopeId(0);
        $this->exceptions->setPath('design/theme/ua_regexp');
        $this->serializer = Bootstrap::getObjectManager()->get(Json::class);
    }
}
