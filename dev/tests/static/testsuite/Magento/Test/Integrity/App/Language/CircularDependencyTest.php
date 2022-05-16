<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test\Integrity\App\Language;

use Magento\Framework\App\Language\Config;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Config\Dom;
use Magento\Framework\Config\DomFactory;
use Magento\Framework\Config\ValidationStateInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class CircularDependencyTest extends TestCase
{
    /**
     * @var Config[][]
     */
    private $packs;

    /**
     * Test circular dependencies between languages
     */
    public function testCircularDependencies()
    {
        $objectManager = new ObjectManager($this);
        $componentRegistrar = new ComponentRegistrar();
        $declaredLanguages = $componentRegistrar->getPaths(ComponentRegistrar::LANGUAGE);
        $validationStateMock = $this->createMock(ValidationStateInterface::class);
        $validationStateMock->method('isValidationRequired')
            ->willReturn(true);
        $domFactoryMock = $this->createMock(DomFactory::class);
        $domFactoryMock->expects($this->any())
            ->method('createDom')
            ->willReturnCallback(
                function ($arguments) use ($validationStateMock) {
                    return new Dom(
                        $arguments['xml'],
                        $validationStateMock,
                        [],
                        null,
                        $arguments['schemaFile']
                    );
                }
            );

        $packs = [];
        foreach ($declaredLanguages as $language) {
            $languageConfig = $objectManager->getObject(
                Config::class,
                [
                    'source' => file_get_contents($language . '/language.xml'),
                    'domFactory' => $domFactoryMock
                ]
            );
            $this->packs[$languageConfig->getVendor()][$languageConfig->getPackage()] = $languageConfig;
            $packs[] = $languageConfig;
        }

        /** @var $languageConfig Config */
        foreach ($packs as $languageConfig) {
            $languages = [];
            /** @var $config Config */
            foreach ($this->collectCircularInheritance($languageConfig) as $config) {
                $languages[] = $config->getVendor() . '/' . $config->getPackage();
            }
            if (!empty($languages)) {
                $this->fail("Circular dependency detected:\n" . implode(' -> ', $languages));
            }
        }
    }

    /**
     * @param Config $languageConfig
     * @param array $languageList
     * @param bool $isCircular
     * @return array|null
     */
    private function collectCircularInheritance(Config $languageConfig, &$languageList = [], &$isCircular = false)
    {
        $packKey = implode('|', [$languageConfig->getVendor(), $languageConfig->getPackage()]);
        if (isset($languageList[$packKey])) {
            $isCircular = true;
        } else {
            $languageList[$packKey] = $languageConfig;
            foreach ($languageConfig->getUses() as $reuse) {
                if (isset($this->packs[$reuse['vendor']][$reuse['package']])) {
                    $this->collectCircularInheritance(
                        $this->packs[$reuse['vendor']][$reuse['package']],
                        $languageList,
                        $isCircular
                    );
                }
            }
        }
        return $isCircular ? $languageList : [];
    }
}
