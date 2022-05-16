<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Persistent\Model;

use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    /**
     * Session model
     *
     * @var Session
     */
    protected $session;

    /**
     * Object manager
     *
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * The existing cookies
     *
     * @var array
     */
    protected $existingCookies;

    public function testSetPersistentCookie()
    {
        $this->assertArrayNotHasKey(Session::COOKIE_NAME, $_COOKIE);
        $key = 'sessionKey';
        $this->session->setKey($key);
        $this->session->setPersistentCookie(1000, '/');
        $this->assertEquals($key, $_COOKIE[Session::COOKIE_NAME]);
    }

    public function testRemovePersistendCookie()
    {
        $_COOKIE[Session::COOKIE_NAME] = 'cookieValue';
        $this->session->removePersistentCookie();
        $this->assertArrayNotHasKey(Session::COOKIE_NAME, $_COOKIE);
    }

    /**
     * @param int $duration
     * @param string $cookieValue
     * @dataProvider renewPersistentCookieDataProvider
     */
    public function testRenewPersistentCookie($duration, $cookieValue = 'cookieValue')
    {
        $_COOKIE[Session::COOKIE_NAME] = $cookieValue;
        $this->session->renewPersistentCookie($duration, '/');
        $this->assertEquals($cookieValue, $_COOKIE[Session::COOKIE_NAME]);
    }

    public function renewPersistentCookieDataProvider()
    {
        return [
            'no duration' => [null],
            'no cookie' => [1000, null],
            'all' => [1000],
        ];
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testLoadByCookieKey()
    {
        /** @var Session $preSession */
        $preSession = $this->objectManager->get(SessionFactory::class)
            ->create()
            ->loadByCookieKey();
        $this->assertNull($preSession->getCustomerId());

        $this->session->setCustomerId(1)->save();
        $this->session->setPersistentCookie(1000, '/');

        /** @var Session $postSession */
        $postSession = $this->objectManager->get(SessionFactory::class)
            ->create()
            ->loadByCookieKey();
        $this->assertEquals(1, $postSession->getCustomerId());
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->session = $this->objectManager->create(
            Session::class
        );
        $this->existingCookies = $_COOKIE;
    }

    protected function tearDown(): void
    {
        $_COOKIE = $this->existingCookies;
    }
}
