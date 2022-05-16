<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Model\Search;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Config\Model\Config\Structure;
use Magento\Config\Model\Config\Structure\Data;
use Magento\Config\Model\Config\Structure\Reader;
use Magento\Framework\App\Area;
use Magento\Framework\App\AreaList;
use Magento\Framework\App\Cache\State;
use Magento\Framework\App\Config\FileResolver;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Config\FileIteratorFactory;
use Magento\Framework\Config\ScopeInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class ConfigTest extends TestCase
{
    /**
     * @return array
     */
    public static function loadDataProvider()
    {
        return [
            'Search by field name' => [
                'Test Field',
                [
                    [
                        'id' => 'test_section/test_group/test_field_1',
                        'type' => null,
                        'name' => 'Test Field',
                        'description' => ' / Test Tab / Test Section / Test Group',
                    ],
                    [
                        'id' => 'test_section/test_group/test_field_2',
                        'type' => null,
                        'name' => 'Test Field',
                        'description' => ' / Test Tab / Test Section / Test Group',
                    ],
                ],
            ],
            'Search by group name' => [
                'Test Group',
                [
                    [
                        'id' => 'test_section/test_group',
                        'type' => null,
                        'name' => 'Test Group',
                        'description' => ' / Test Tab / Test Section',
                    ],
                ],
            ],
            'Search by section name' => [
                'Test Section',
                [
                    [
                        'id' => '/test_section',
                        'type' => null,
                        'name' => 'Test Section',
                        'description' => ' / Test Tab',
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider loadDataProvider
     * @magentoConfigFixture current_store general/store_information/name Foo
     */
    public function testLoad($query, $expectedResult)
    {
        /** @var Config $configSearch */
        $configSearch = $this->getConfigSearchInstance();
        $configSearch->setQuery($query);
        $configSearch->load();

        /** SUT Execution */
        $searchResults = $configSearch->getResults();

        /** Ensure that search results are correct */
        $this->assertCount(count($expectedResult), $searchResults, 'Quantity of search result items is invalid.');
        foreach ($expectedResult as $itemIndex => $expectedItem) {
            /** Validate URL to item */
            $elementPathParts = explode('/', $expectedItem['id']);
            // filter empty values
            $elementPathParts = array_values(array_filter($elementPathParts));
            foreach ($elementPathParts as $elementPathPart) {
                $this->assertStringContainsString(
                    $elementPathPart,
                    $searchResults[$itemIndex]['url'],
                    'Item URL is invalid.'
                );
            }
            unset($searchResults[$itemIndex]['url']);

            /** Validate other item data */
            $this->assertEquals($expectedItem, $searchResults[$itemIndex], "Data of item #$itemIndex is invalid.");
        }
    }

    /**
     * @return Config
     */
    private function getConfigSearchInstance()
    {
        Bootstrap::getInstance()->reinitialize([
            State::PARAM_BAN_CACHE => true,
        ]);
        Bootstrap::getObjectManager()
            ->get(ScopeInterface::class)
            ->setCurrentScope(FrontNameResolver::AREA_CODE);
        Bootstrap::getObjectManager()->get(AreaList::class)
            ->getArea(FrontNameResolver::AREA_CODE)
            ->load(Area::PART_CONFIG);

        Bootstrap::getObjectManager()->configure([
            'preferences' => [
                AuthorizationInterface::class => AuthorizationMock::class
            ]
        ]);

        $fileResolverMock = $this->getMockBuilder(FileResolver::class)->disableOriginalConstructor()->getMock();
        $fileIteratorFactory = Bootstrap::getObjectManager()->get(FileIteratorFactory::class);
        $fileIterator = $fileIteratorFactory->create(
            [__DIR__ . '/_files/test_config.xml']
        );
        $fileResolverMock->expects($this->any())->method('get')->willReturn($fileIterator);

        $objectManager = Bootstrap::getObjectManager();
        /** @var Reader $structureReader */
        $structureReader = $objectManager->create(
            Reader::class,
            ['fileResolver' => $fileResolverMock]
        );
        /** @var Data $structureData */
        $structureData = $objectManager->create(
            Data::class,
            ['reader' => $structureReader]
        );
        /** @var Structure $structure */
        $structure = $objectManager->create(
            Structure::class,
            ['structureData' => $structureData]
        );

        return $objectManager->create(
            Config::class,
            ['configStructure' => $structure]
        );
    }
}
