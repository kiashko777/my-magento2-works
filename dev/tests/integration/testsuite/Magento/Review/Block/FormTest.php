<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Review\Block;

use Magento\Framework\App\Area;
use Magento\Framework\App\Config\Value;
use Magento\Framework\App\ReinitableConfig;
use Magento\Framework\App\State;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

class FormTest extends TestCase
{
    /**
     * @var ObjectManager;
     */
    private $objectManager;

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture Magento/Review/_files/config.php
     * @dataProvider getCorrectFlagDataProvider
     */
    public function testGetCorrectFlag(
        $path,
        $scope,
        $scopeId,
        $value,
        $expectedResult
    )
    {
        /** @var State $appState */
        $appState = $this->objectManager->get(State::class);
        $appState->setAreaCode(Area::AREA_FRONTEND);

        /** @var Value $config */
        $config = $this->objectManager->create(Value::class);
        $config->setPath($path);
        $config->setScope($scope);
        $config->setScopeId($scopeId);
        $config->setValue($value);
        $config->save();
        /** @var ReinitableConfig $reinitableConfig */
        $reinitableConfig = $this->objectManager->create(ReinitableConfig::class);
        $reinitableConfig->reinit();

        /** @var Form $form */
        $form = $this->objectManager->create(Form::class);
        $result = $form->getAllowWriteReviewFlag();
        $this->assertEquals($result, $expectedResult);
    }

    public function getCorrectFlagDataProvider()
    {
        return [
            [
                'path' => 'catalog/review/allow_guest',
                'scope' => 'websites',
                'scopeId' => '1',
                'value' => 0,
                'expectedResult' => false,
            ],
            [
                'path' => 'catalog/review/allow_guest',
                'scope' => 'websites',
                'scopeId' => '1',
                'value' => 1,
                'expectedResult' => true
            ]
        ];
    }

    protected function setUp(): void
    {
        $this->objectManager = $this->getObjectManager();

        parent::setUp();
    }

    private function getObjectManager()
    {
        return Bootstrap::getObjectManager();
    }
}
