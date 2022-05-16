<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Search\Model;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoDbIsolation disabled
 * @magentoDataFixture Magento/Search/_files/synonym_reader.php
 */
class SynonymReaderTest extends TestCase
{
    /**
     * @var SynonymReader
     */
    private $model;

    /**
     * @return array
     */
    public function loadByPhraseDataProvider(): array
    {
        return [
            [
                'ELIZABETH', []
            ],
            [
                '-+<(ELIZABETH)>*~', []
            ],
            [
                'ENGLISH', [['synonyms' => 'british,english', 'store_id' => 1, 'website_id' => 0]]
            ],
            [
                'English', [['synonyms' => 'british,english', 'store_id' => 1, 'website_id' => 0]]
            ],
            [
                'QUEEN', [['synonyms' => 'queen,monarch', 'store_id' => 1, 'website_id' => 0]]
            ],
            [
                'Monarch', [['synonyms' => 'queen,monarch', 'store_id' => 1, 'website_id' => 0]]
            ],
            [
                '-+<(Monarch)>*~', [['synonyms' => 'queen,monarch', 'store_id' => 1, 'website_id' => 0]]
            ],
            [
                'MONARCH English', [
                ['synonyms' => 'queen,monarch', 'store_id' => 1, 'website_id' => 0],
                ['synonyms' => 'british,english', 'store_id' => 1, 'website_id' => 0]
            ]
            ],
            [
                'query_value', []
            ],
            [
                'query_value+', []
            ],
            [
                'query_value-', []
            ],
            [
                'query_@value', []
            ],
            [
                'query_value+@', []
            ],
            [
                '<', []
            ],
            [
                '>', []
            ],
            [
                '<english>', [['synonyms' => 'british,english', 'store_id' => 1, 'website_id' => 0]]
            ],
        ];
    }

    /**
     * @param string $phrase
     * @param array $expectedResult
     * @dataProvider loadByPhraseDataProvider
     */
    public function testLoadByPhrase(string $phrase, array $expectedResult)
    {
        $data = $this->model->loadByPhrase($phrase)->getData();

        $i = 0;
        foreach ($expectedResult as $r) {
            $this->assertEquals($r['synonyms'], $data[$i]['synonyms']);
            $this->assertEquals($r['store_id'], $data[$i]['store_id']);
            $this->assertEquals($r['website_id'], $data[$i]['website_id']);
            ++$i;
        }
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->model = $objectManager->get(SynonymReader::class);
    }
}
