<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Tax\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\ObjectManagerInterface;
use Magento\Tax\Api\Data\TaxRateInterfaceFactory;
use Magento\Tax\Api\Data\TaxRuleInterface;
use Magento\Tax\Api\Data\TaxRuleInterfaceFactory;
use Magento\Tax\Api\TaxRateRepositoryInterface;
use Magento\Tax\Api\TaxRuleRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * TaxRuleFixtureFactory is meant to help in testing tax by creating/destroying tax rules/classes/rates easily.
 */
class TaxRuleFixtureFactory
{
    /**
     * Object Manager
     *
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    public function __construct()
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->dataObjectHelper = $this->objectManager->create(DataObjectHelper::class);
    }

    /**
     * Helper to create tax rules.
     *
     * @param array $rulesData Keys match populateWithArray
     * @return array code => rule id
     */
    public function createTaxRules($rulesData)
    {
        /** @var TaxRuleInterfaceFactory $taxRuleFactory */
        $taxRuleFactory = $this->objectManager->create(TaxRuleInterfaceFactory::class);
        /** @var TaxRuleRepositoryInterface $taxRuleService */
        $taxRuleService = $this->objectManager->create(TaxRuleRepositoryInterface::class);

        $rules = [];
        foreach ($rulesData as $ruleData) {
            $taxRule = $taxRuleFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $taxRule,
                $ruleData,
                TaxRuleInterface::class
            );

            $rules[$ruleData['code']] = $taxRuleService->save($taxRule)->getId();
        }

        return $rules;
    }

    /**
     * Helper function that deletes tax rules
     *
     * @param int[] $ruleIds
     */
    public function deleteTaxRules($ruleIds)
    {
        /** @var TaxRuleRepositoryInterface $taxRuleService */
        $taxRuleService = $this->objectManager->create(TaxRuleRepositoryInterface::class);

        foreach ($ruleIds as $ruleId) {
            $taxRuleService->deleteById($ruleId);
        }
    }

    /**
     * Helper function that creates rates based on a set of input percentages.
     *
     * Returns a map of percentage => rate
     *
     * @param array $ratesData array of rate data, keys are 'country', 'region' and 'percentage'
     * @return int[] Tax Rate Id
     */
    public function createTaxRates($ratesData)
    {
        /** @var TaxRateInterfaceFactory $taxRateFactory */
        $taxRateFactory = $this->objectManager->create(TaxRateInterfaceFactory::class);
        /** @var TaxRateRepositoryInterface $taxRateService */
        $taxRateService = $this->objectManager->create(TaxRateRepositoryInterface::class);

        $rates = [];
        foreach ($ratesData as $rateData) {
            $code = "{$rateData['country']} - {$rateData['region']} - {$rateData['percentage']}";
            $postcode = '*';
            if (isset($rateData['postcode'])) {
                $postcode = $rateData['postcode'];
                $code = $code . " - " . $postcode;
            }

            $taxRate = $taxRateFactory->create();
            $taxRate->setTaxCountryId($rateData['country'])
                ->setTaxRegionId($rateData['region'])
                ->setTaxPostcode($postcode)
                ->setCode($code)
                ->setRate($rateData['percentage']);

            $rates[$code] = $taxRateService->save($taxRate)->getId();
        }
        return $rates;
    }

    /**
     * Helper function that deletes tax rates
     *
     * @param int[] $rateIds
     */
    public function deleteTaxRates($rateIds)
    {
        /** @var TaxRateRepositoryInterface $taxRateService */
        $taxRateService = $this->objectManager->create(TaxRateRepositoryInterface::class);
        foreach ($rateIds as $rateId) {
            $taxRateService->deleteById($rateId);
        }
    }

    /**
     * Helper function that creates tax classes based on input.
     *
     * @param array $classesData Keys include 'name' and 'type'
     * @return array ClassName => ClassId
     */
    public function createTaxClasses($classesData)
    {
        $classes = [];
        foreach ($classesData as $classData) {
            /** @var ClassModel $class */
            $class = $this->objectManager->create(ClassModel::class)
                ->setClassName($classData['name'])
                ->setClassType($classData['type'])
                ->save();
            $classes[$classData['name']] = $class->getId();
        }
        return $classes;
    }

    /**
     * Helper function that deletes tax classes
     *
     * @param int[] $classIds
     */
    public function deleteTaxClasses($classIds)
    {
        /** @var ClassModel $class */
        $class = $this->objectManager->create(ClassModel::class);
        foreach ($classIds as $classId) {
            $class->load($classId);
            $class->delete();
        }
    }
}
