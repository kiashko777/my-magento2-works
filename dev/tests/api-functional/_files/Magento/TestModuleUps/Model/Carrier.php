<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TestModuleUps\Model;

use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Directory\Helper\Data;
use Magento\Directory\Model\CountryFactory;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\AsyncClientInterface;
use Magento\Framework\HTTP\ClientFactory;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\Xml\Security;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Rate\Result\ProxyDeferredFactory;
use Magento\Shipping\Model\Simplexml\ElementFactory;
use Magento\Shipping\Model\Tracking\Result\ErrorFactory;
use Magento\Shipping\Model\Tracking\Result\StatusFactory;
use Magento\Shipping\Model\Tracking\ResultFactory;
use Magento\Ups\Helper\Config;
use Psr\Log\LoggerInterface;

/**
 * Mock UPS shipping implementation
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Carrier extends \Magento\Ups\Model\Carrier
{
    /**
     * @var MockResponseBodyLoader
     */
    private $mockResponseLoader;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param Security $xmlSecurity
     * @param ElementFactory $xmlElFactory
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateFactory
     * @param MethodFactory $rateMethodFactory
     * @param ResultFactory $trackFactory
     * @param ErrorFactory $trackErrorFactory
     * @param StatusFactory $trackStatusFactory
     * @param RegionFactory $regionFactory
     * @param CountryFactory $countryFactory
     * @param CurrencyFactory $currencyFactory
     * @param Data $directoryData
     * @param StockRegistryInterface $stockRegistry
     * @param FormatInterface $localeFormat
     * @param Config $configHelper
     * @param ClientFactory $httpClientFactory
     * @param array $data
     * @param AsyncClientInterface $asyncHttpClient
     * @param ProxyDeferredFactory $proxyDeferredFactory
     * @param MockResponseBodyLoader $mockResponseLoader
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ScopeConfigInterface          $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory  $rateErrorFactory,
        LoggerInterface                                    $logger,
        Security                                                    $xmlSecurity,
        ElementFactory            $xmlElFactory,
        \Magento\Shipping\Model\Rate\ResultFactory                  $rateFactory,
        MethodFactory $rateMethodFactory,
        ResultFactory              $trackFactory,
        ErrorFactory        $trackErrorFactory,
        StatusFactory       $trackStatusFactory,
        RegionFactory                      $regionFactory,
        CountryFactory                     $countryFactory,
        CurrencyFactory                    $currencyFactory,
        Data                              $directoryData,
        StockRegistryInterface        $stockRegistry,
        FormatInterface                   $localeFormat,
        Config                                                      $configHelper,
        ClientFactory                                               $httpClientFactory,
        AsyncClientInterface                                        $asyncHttpClient,
        ProxyDeferredFactory                                        $proxyDeferredFactory,
        MockResponseBodyLoader                                      $mockResponseLoader,
        array                                                       $data = []
    )
    {
        parent::__construct(
            $scopeConfig,
            $rateErrorFactory,
            $logger,
            $xmlSecurity,
            $xmlElFactory,
            $rateFactory,
            $rateMethodFactory,
            $trackFactory,
            $trackErrorFactory,
            $trackStatusFactory,
            $regionFactory,
            $countryFactory,
            $currencyFactory,
            $directoryData,
            $stockRegistry,
            $localeFormat,
            $configHelper,
            $httpClientFactory,
            $data,
            $asyncHttpClient,
            $proxyDeferredFactory
        );
        $this->mockResponseLoader = $mockResponseLoader;
    }

    /**
     * @inheritdoc
     */
    protected function _getCgiQuotes()
    {
        $responseBody = $this->mockResponseLoader->loadForRequest($this->_rawRequest->getDestCountry());
        return $this->_parseCgiResponse($responseBody);
    }
}
