<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogSearch\Controller;

use Magento\Framework\App\Cache\StateInterface;
use Magento\Framework\App\Config\MutableScopeConfigInterface;
use Magento\Framework\App\MutableScopeConfig;
use Magento\Framework\Escaper;
use Magento\Framework\Locale\ResolverInterface;
use Magento\PageCache\Model\Cache\Type;
use Magento\Search\Model\PopularSearchTerms;
use Magento\Search\Model\Query;
use Magento\Store\Model\ScopeInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractController;

/**
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 * @magentoDataFixture Magento/CatalogSearch/_files/full_reindex.php
 */
class ResultTest extends AbstractController
{
    /**
     * @magentoDataFixture Magento/CatalogSearch/_files/query.php
     */
    public function testIndexActionTranslation()
    {
        $this->markTestSkipped('MAGETWO-44910');
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get(ResolverInterface::class)->setLocale('de_DE');

        $this->getRequest()->setParam('q', 'query_text');
        $this->dispatch('catalogsearch/result');

        $responseBody = $this->getResponse()->getBody();
        $this->assertStringNotContainsString('for="search">Search', $responseBody);
        $this->assertStringMatchesFormat('%aSuche%S%a', $responseBody);

        $this->assertStringNotContainsString('Search entire store here...', $responseBody);
        $this->assertStringContainsString('Den gesamten Shop durchsuchen...', $responseBody);
    }

    /**
     * @magentoDbIsolation disabled
     */
    public function testIndexActionXSSQueryVerification()
    {
        $escaper = Bootstrap::getObjectManager()
            ->get(Escaper::class);
        $this->getRequest()->setParam('q', '<script>alert(1)</script>');
        $this->dispatch('catalogsearch/result');

        $responseBody = $this->getResponse()->getBody();
        $data = '<script>alert(1)</script>';
        $this->assertStringNotContainsString($data, $responseBody);
        $this->assertStringContainsString($escaper->escapeHtml($data), $responseBody);
    }

    /**
     * @magentoDataFixture Magento/CatalogSearch/_files/query_redirect.php
     */
    public function testRedirect()
    {
        $this->dispatch('/catalogsearch/result/?q=query_text');
        $responseBody = $this->getResponse();

        $this->assertTrue($responseBody->isRedirect());
    }

    /**
     * @magentoDataFixture Magento/CatalogSearch/_files/query_redirect.php
     */
    public function testNoRedirectIfCurrentUrlAndRedirectTermAreSame()
    {
        $this->dispatch('/catalogsearch/result/?q=query_text&cat=41');
        $responseBody = $this->getResponse();

        $this->assertFalse($responseBody->isRedirect());
    }

    /**
     * @magentoDataFixture Magento/CatalogSearch/_files/query.php
     */
    public function testPopularity()
    {
        $objectManager = Bootstrap::getObjectManager();

        /** @var $query Query */
        $query = $objectManager->create(Query::class);
        $query->loadByQueryText('query_text');
        $this->assertEquals(1, $query->getPopularity());

        $this->dispatch('catalogsearch/searchTermsLog/save?q=query_text');

        $responseBody = $this->getResponse()->getBody();
        $data = '"success":true';
        $this->assertStringContainsString($data, $responseBody);

        $query->loadByQueryText('query_text');
        $this->assertEquals(2, $query->getPopularity());
    }

    /**
     * @magentoDataFixture Magento/CatalogSearch/_files/popular_query.php
     * @magentoDataFixture Magento/CatalogSearch/_files/query.php
     */
    public function testPopularSearch()
    {
        $this->cacheAndPopularitySetup();
        $objectManager = Bootstrap::getObjectManager();

        /** @var $query Query */
        $query = $objectManager->create(Query::class);
        $query->loadByQueryText('popular_query_text');
        $this->assertEquals(100, $query->getPopularity());

        $this->dispatch('/catalogsearch/result/?q=popular_query_text');

        $responseBody = $this->getResponse()->getBody();
        $this->assertStringContainsString('Search results for: &#039;popular_query_text&#039;', $responseBody);
        $this->assertStringContainsString('/catalogsearch/searchTermsLog/save/', $responseBody);

        $query->loadByQueryText('popular_query_text');
        $this->assertEquals(100, $query->getPopularity());
    }

    private function cacheAndPopularitySetup()
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var $scopeConfig MutableScopeConfig */
        $scopeConfig = $objectManager->get(MutableScopeConfigInterface::class);
        $scopeConfig->setValue(
            PopularSearchTerms::XML_PATH_MAX_COUNT_CACHEABLE_SEARCH_TERMS,
            1,
            ScopeInterface::SCOPE_STORE
        );

        /** @var $cacheState StateInterface */
        $cacheState = $objectManager->get(StateInterface::class);
        $cacheState->setEnabled(Type::TYPE_IDENTIFIER, true);

        /** @var $fpc Type */
        $fpc = $objectManager->get(Type::class);
        $fpc->clean();
    }

    /**
     * @magentoDataFixture Magento/CatalogSearch/_files/popular_query.php
     * @magentoDataFixture Magento/CatalogSearch/_files/query.php
     */
    public function testPopularSearchWithAdditionalRequestParameters()
    {
        $this->cacheAndPopularitySetup();
        $objectManager = Bootstrap::getObjectManager();

        /** @var $query Query */
        $query = $objectManager->create(Query::class);
        $query->loadByQueryText('popular_query_text');
        $this->assertEquals(100, $query->getPopularity());

        $this->dispatch('/catalogsearch/result/?q=popular_query_text&additional_parameters=some');

        $responseBody = $this->getResponse()->getBody();
        $this->assertStringContainsString('Search results for: &#039;popular_query_text&#039;', $responseBody);
        $this->assertStringNotContainsString('/catalogsearch/searchTermsLog/save/', $responseBody);

        $query->loadByQueryText('popular_query_text');
        $this->assertEquals(101, $query->getPopularity());
    }

    /**
     * @magentoDataFixture Magento/CatalogSearch/_files/popular_query.php
     * @magentoDataFixture Magento/CatalogSearch/_files/query.php
     */
    public function testNotPopularSearch()
    {
        $this->cacheAndPopularitySetup();
        $objectManager = Bootstrap::getObjectManager();

        /** @var $query Query */
        $query = $objectManager->create(Query::class);
        $query->loadByQueryText('query_text');
        $this->assertEquals(1, $query->getPopularity());

        $this->dispatch('/catalogsearch/result/?q=query_text');

        $responseBody = $this->getResponse()->getBody();
        $this->assertStringContainsString('Search results for: &#039;query_text&#039;', $responseBody);
        $this->assertStringNotContainsString('/catalogsearch/searchTermsLog/save/', $responseBody);

        $query->loadByQueryText('query_text');
        $this->assertEquals(2, $query->getPopularity());
    }
}
