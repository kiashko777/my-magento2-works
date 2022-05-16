<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\ObjectManager\TestAsset;

class ComplexDependencies
{
    /**
     * @var Basic
     */
    private $basic;

    /**
     * @var BasicInjection
     */
    private $basicInjection;

    /**
     * @var DependsOnInterface
     */
    private $dependsOnInterface;

    /**
     * @var HasOptionalParameters
     */
    private $hasOptionalParameters;

    /**
     * @var TestAssetInterface
     */
    private $testAssetInterface;

    /**
     * @var ConstructorNineArguments
     */
    private $constructorNineArguments;

    /**
     * @var DependsOnAlias
     */
    private $dependsOnAlias;

    /**
     * @param Basic $basic
     * @param BasicInjection $basicInjection
     * @param DependsOnInterface $dependsOnInterface
     * @param HasOptionalParameters $hasOptionalParameters
     * @param TestAssetInterface $testAssetInterface
     * @param ConstructorNineArguments $constructorNineArguments
     * @param DependsOnAlias $dependsOnAlias
     */
    public function __construct(
        Basic                    $basic,
        BasicInjection           $basicInjection,
        DependsOnInterface       $dependsOnInterface,
        HasOptionalParameters    $hasOptionalParameters,
        TestAssetInterface       $testAssetInterface,
        ConstructorNineArguments $constructorNineArguments,
        DependsOnAlias           $dependsOnAlias
    )
    {
        $this->basic = $basic;
        $this->basicInjection = $basicInjection;
        $this->dependsOnInterface = $dependsOnInterface;
        $this->hasOptionalParameters = $hasOptionalParameters;
        $this->testAssetInterface = $testAssetInterface;
        $this->constructorNineArguments = $constructorNineArguments;
        $this->dependsOnAlias = $dependsOnAlias;
    }

    /**
     * @return DependsOnAlias
     */
    public function getDependsOnAlias()
    {
        return $this->dependsOnAlias;
    }

    /**
     * @return Basic
     */
    public function getBasic()
    {
        return $this->basic;
    }

    /**
     * @return BasicInjection
     */
    public function getBasicInjection()
    {
        return $this->basicInjection;
    }

    /**
     * @return DependsOnInterface
     */
    public function getDependsOnInterface()
    {
        return $this->dependsOnInterface;
    }

    /**
     * @return HasOptionalParameters
     */
    public function getHasOptionalParameters()
    {
        return $this->hasOptionalParameters;
    }

    /**
     * @return TestAssetInterface
     */
    public function getTestAssetInterface()
    {
        return $this->testAssetInterface;
    }

    /**
     * @return ConstructorNineArguments
     */
    public function getConstructorNineArguments()
    {
        return $this->constructorNineArguments;
    }
}
