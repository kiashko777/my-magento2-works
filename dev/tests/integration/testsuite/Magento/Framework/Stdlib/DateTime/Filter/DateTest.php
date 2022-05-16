<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Stdlib\DateTime\Filter;

use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var ResolverInterface
     */
    private $localeResolver;

    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @var Date
     */
    private $dateFilter;

    /**
     * @param string $inputData
     * @param string $expectedDate
     *
     * @dataProvider filterDataProvider
     */
    public function testFilter($inputData, $expectedDate)
    {
        $this->markTestSkipped(
            'Input data not realistic with actual request payload from admin UI. See MAGETWO-59810'
        );
        $this->assertEquals($expectedDate, $this->dateFilter->filter($inputData));
    }

    /**
     * @return array
     */
    public function filterDataProvider()
    {
        return [
            ['2000-01-01', '2000-01-01'],
            ['2014-03-30T02:30:00', '2014-03-30'],
            ['12/31/2000', '2000-12-31']
        ];
    }

    /**
     * @param string $locale
     * @param string $inputData
     * @param string $expectedDate
     *
     * @dataProvider localeDateFilterProvider
     * @return void
     */
    public function testLocaleDateFilter($locale, $inputData, $expectedDate)
    {
        $this->localeResolver->setLocale($locale);
        $this->assertEquals($expectedDate, $this->dateFilter->filter($inputData));
    }

    /**
     * @return array
     */
    public function localeDateFilterProvider()
    {
        return [
            ['en_US', '01/02/2010', '2010-01-02'],
            ['fr_FR', '01/02/2010', '2010-02-01'],
            ['de_DE', '01/02/2010', '2010-02-01'],
        ];
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();

        $this->localeResolver = $this->objectManager->get(ResolverInterface::class);

        $this->localeDate = $this->objectManager->get(TimezoneInterface::class, [
            'localeResolver' => $this->localeResolver
        ]);

        $this->dateFilter = $this->objectManager->get(Date::class, [
            'localeDate' => $this->localeDate
        ]);
    }
}
