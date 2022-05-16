<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Stdlib\Cookie;

use PHPUnit\Framework\TestCase;

class PhpCookieReaderTest extends TestCase
{
    const NAME = 'cookie-name';
    const VALUE = 'cookie-val';
    const DEFAULT_VAL = 'default-val';
    /**
     * @var array
     */
    protected $preTestCookies;
    /**
     * @var PhpCookieReader
     */
    protected $model;

    public function testGetCookieExists()
    {
        $this->assertSame(self::VALUE, $this->model->getCookie(self::NAME, self::DEFAULT_VAL));
    }

    public function testGetCookieDefault()
    {
        $this->assertSame(self::DEFAULT_VAL, $this->model->getCookie('cookies does not exist', self::DEFAULT_VAL));
        $this->assertSame(self::DEFAULT_VAL, $this->model->getCookie(null, self::DEFAULT_VAL));
    }

    public function testGetCookieNoDefault()
    {
        $this->assertNull($this->model->getCookie('cookies does not exist'));
        $this->assertNull($this->model->getCookie(null));
    }

    protected function setUp(): void
    {
        $this->preTestCookies = $_COOKIE;
        $_COOKIE = [];
        $_COOKIE[self::NAME] = self::VALUE;
        $this->model = new PhpCookieReader();
    }

    protected function tearDown(): void
    {
        $_COOKIE = $this->preTestCookies;
    }
}
