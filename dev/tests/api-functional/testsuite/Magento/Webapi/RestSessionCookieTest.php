<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Webapi;

use Magento\Framework\Module\Manager;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\HttpClient\CurlClientWithCookies;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Class for RestSessionCookieTest
 */
class RestSessionCookieTest extends WebapiAbstract
{

    private $moduleManager;
    private $objectManager;

    /**
     * Check for non exist cookie PHPSESSID
     */
    public function testRestSessionNoCookie()
    {
        $this->_markTestAsRestOnly();
        /** @var $curlClient CurlClientWithCookies */

        $curlClient = $this->objectManager
            ->get(CurlClientWithCookies::class);
        $phpSessionCookieName =
            [
                'cookie_name' => 'PHPSESSID',
            ];

        $response = $curlClient->get('/rest/V1/directory/countries', []);

        $cookie = $this->findCookie($phpSessionCookieName['cookie_name'], $response['cookies']);
        $this->assertNull($cookie);
    }

    /**
     * Find cookie with given name in the list of cookies
     *
     * @param string $cookieName
     * @param array $cookies
     * @return $cookie|null
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    private function findCookie($cookieName, $cookies)
    {
        foreach ($cookies as $cookieIndex => $cookie) {
            if ($cookie['name'] === $cookieName) {
                return $cookie;
            }
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->moduleManager = $this->objectManager->get(Manager::class);
        if ($this->moduleManager->isEnabled('Magento_B2b')) {
            $this->markTestSkipped('Skipped, because this logic is rewritten on B2B.');
        }
    }
}
