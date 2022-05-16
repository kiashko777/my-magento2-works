<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreStart
namespace {
    $mockPHPFunctions = false;
}

namespace Magento\Framework\Session {

    use Magento\Framework\App\Area;
    use Magento\Framework\App\DeploymentConfig;
    use Magento\Framework\App\Request\Http;
    use Magento\Framework\App\RequestInterface;
    use Magento\Framework\App\State;
    use Magento\Framework\Config\ScopeInterface;
    use Magento\Framework\Exception\SessionException;
    use Magento\Framework\Session\Config\ConfigInterface;
    use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
    use Magento\Framework\Stdlib\CookieManagerInterface;
    use Magento\TestFramework\Helper\Bootstrap;
    use Magento\TestFramework\ObjectManager;
    use PHPUnit\Framework\MockObject\MockObject;
    use PHPUnit\Framework\TestCase;
    use ReflectionMethod;

    // @codingStandardsIgnoreEnd

    /**
     * Mock session_status if in test mode, or continue normal execution otherwise
     *
     * @return int Session status code
     */
    function session_status()
    {
        global $mockPHPFunctions;
        if ($mockPHPFunctions) {
            return PHP_SESSION_NONE;
        }
        return call_user_func_array('\session_status', func_get_args());
    }

    function headers_sent()
    {
        global $mockPHPFunctions;
        if ($mockPHPFunctions) {
            return false;
        }
        return call_user_func_array('\headers_sent', func_get_args());
    }

    /**
     * Mock ini_set global function
     *
     * @param string $varName
     * @param string $newValue
     * @return bool|string
     */
    function ini_set($varName, $newValue)
    {
        global $mockPHPFunctions;
        if ($mockPHPFunctions) {
            SessionManagerTest::$isIniSetInvoked[$varName] = $newValue;
            return true;
        }
        return call_user_func_array('\ini_set', [$varName, $newValue]);
    }

    /**
     * Mock session_set_save_handler global function
     *
     * @return bool
     */
    function session_set_save_handler()
    {
        global $mockPHPFunctions;
        if ($mockPHPFunctions) {
            SessionManagerTest::$isSessionSetSaveHandlerInvoked = true;
            return true;
        }
        return call_user_func_array('\session_set_save_handler', func_get_args());
    }

    /**
     * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
     */
    class SessionManagerTest extends TestCase
    {
        /**
         * @var string[]
         */
        public static $isIniSetInvoked = [];

        /**
         * @var bool
         */
        public static $isSessionSetSaveHandlerInvoked;

        /**
         * @var SessionManagerInterface
         */
        private $model;

        /**
         * @var SidResolverInterface
         */
        private $sidResolver;

        /**
         * @var string
         */
        private $sessionName;

        /**
         * @var ObjectManager
         */
        private $objectManager;

        /**
         * @var RequestInterface
         */
        private $request;

        /**
         * @var State|MockObject
         */
        private $appState;

        public function testSessionNameFromIni()
        {
            $this->initializeModel();
            $this->model->start();
            $this->assertSame($this->sessionName, $this->model->getName());
            $this->model->destroy();
        }

        private function initializeModel(): void
        {
            $this->model = $this->objectManager->create(
                SessionManager::class,
                [
                    'sidResolver' => $this->sidResolver
                ]
            );
        }

        public function testSessionUseOnlyCookies()
        {
            $this->initializeModel();
            $expectedValue = '1';
            $sessionUseOnlyCookies = ini_get('session.use_only_cookies');
            $this->assertSame($expectedValue, $sessionUseOnlyCookies);
        }

        public function testGetData()
        {
            $this->initializeModel();
            $this->model->setData(['test_key' => 'test_value']);
            $this->assertEquals('test_value', $this->model->getData('test_key', true));
            $this->assertNull($this->model->getData('test_key'));
        }

        public function testGetSessionId()
        {
            $this->initializeModel();
            $this->assertEquals(session_id(), $this->model->getSessionId());
        }

        public function testGetName()
        {
            $this->initializeModel();
            $this->assertEquals(session_name(), $this->model->getName());
        }

        public function testSetName()
        {
            $this->initializeModel();
            $this->model->destroy();
            $this->model->setName('test');
            $this->model->start();
            $this->assertEquals('test', $this->model->getName());
        }

        public function testDestroy()
        {
            $this->initializeModel();
            $data = ['key' => 'value'];
            $this->model->setData($data);

            $this->assertEquals($data, $this->model->getData());
            $this->model->destroy();

            $this->assertEquals([], $this->model->getData());
        }

        public function testSetSessionId()
        {
            $this->initializeModel();
            $this->assertNotEmpty($this->model->getSessionId());
            $this->appState->expects($this->any())
                ->method('getAreaCode')
                ->willReturn(Area::AREA_FRONTEND);

            $this->model->setSessionId('test');
            $this->assertEquals('test', $this->model->getSessionId());
            /* Use not valid identifier */
            $this->model->setSessionId('test_id');
            $this->assertEquals('test', $this->model->getSessionId());
        }

        public function testGetSessionIdForHost()
        {
            $this->initializeModel();
            $_SERVER['HTTP_HOST'] = 'localhost';
            $this->model->start();
            $this->assertEmpty($this->model->getSessionIdForHost('localhost'));
            $this->assertNotEmpty($this->model->getSessionIdForHost('test'));
            $this->model->destroy();
        }

        public function testIsValidForHost()
        {
            $this->initializeModel();
            $_SERVER['HTTP_HOST'] = 'localhost';
            $this->model->start();

            $reflection = new ReflectionMethod($this->model, '_addHost');
            $reflection->setAccessible(true);
            $reflection->invoke($this->model);

            $this->assertFalse($this->model->isValidForHost('test.com'));
            $this->assertTrue($this->model->isValidForHost('localhost'));
            $this->model->destroy();
        }

        public function testStartAreaNotSet()
        {
            $this->expectException(SessionException::class);
            $this->expectExceptionMessage('Area code not set: Area code must be set before starting a session.');

            $scope = $this->objectManager->get(ScopeInterface::class);
            $appState = new State($scope);

            /**
             * Must be created by "new" in order to get a real Magento\Framework\App\State object that
             * is not overridden in the TestFramework
             *
             * @var SessionManager _model
             */
            $this->model = new SessionManager(
                $this->objectManager->get(Http::class),
                $this->sidResolver,
                $this->objectManager->get(ConfigInterface::class),
                $this->objectManager->get(SaveHandlerInterface::class),
                $this->objectManager->get(ValidatorInterface::class),
                $this->objectManager->get(StorageInterface::class),
                $this->objectManager->get(CookieManagerInterface::class),
                $this->objectManager->get(CookieMetadataFactory::class),
                $appState
            );

            global $mockPHPFunctions;
            $mockPHPFunctions = true;
            $this->model->start();
        }

        /**
         * @param string $saveMethod
         * @dataProvider dataConstructor
         *
         * @return void
         */
        public function testConstructor(string $saveMethod): void
        {
            global $mockPHPFunctions;
            $mockPHPFunctions = true;

            $deploymentConfigMock = $this->createMock(DeploymentConfig::class);
            $deploymentConfigMock->method('get')
                ->willReturnCallback(function ($configPath) use ($saveMethod) {
                    switch ($configPath) {
                        case Config::PARAM_SESSION_SAVE_METHOD:
                            return $saveMethod;
                        case Config::PARAM_SESSION_CACHE_LIMITER:
                            return 'private_no_expire';
                        case Config::PARAM_SESSION_SAVE_PATH:
                            return 'explicit_save_path';
                        default:
                            return null;
                    }
                });
            $sessionConfig = $this->objectManager->create(Config::class, ['deploymentConfig' => $deploymentConfigMock]);
            $saveHandler = $this->objectManager->create(SaveHandler::class, ['sessionConfig' => $sessionConfig]);

            $this->model = $this->objectManager->create(
                SessionManager::class,
                [
                    'sidResolver' => $this->sidResolver,
                    'saveHandler' => $saveHandler,
                    'sessionConfig' => $sessionConfig,
                ]
            );
            $this->assertEquals($saveMethod, $sessionConfig->getOption('session.save_handler'));
            $this->assertEquals('private_no_expire', $sessionConfig->getOption('session.cache_limiter'));
            $this->assertEquals('explicit_save_path', $sessionConfig->getOption('session.save_path'));
            $this->assertArrayHasKey('session.use_only_cookies', self::$isIniSetInvoked);
            $this->assertEquals('1', self::$isIniSetInvoked['session.use_only_cookies']);
            foreach ($sessionConfig->getOptions() as $option => $value) {
                if ($option === 'session.save_handler' && $value !== 'memcached') {
                    $this->assertArrayNotHasKey('session.save_handler', self::$isIniSetInvoked);
                } else {
                    $this->assertArrayHasKey($option, self::$isIniSetInvoked);
                    $this->assertEquals($value, self::$isIniSetInvoked[$option]);
                }
            }
            $this->assertTrue(self::$isSessionSetSaveHandlerInvoked);
        }

        /**
         * @return array
         */
        public function dataConstructor(): array
        {
            return [
                [Config::PARAM_SESSION_SAVE_METHOD => 'db'],
                [Config::PARAM_SESSION_SAVE_METHOD => 'redis'],
                [Config::PARAM_SESSION_SAVE_METHOD => 'memcached'],
                [Config::PARAM_SESSION_SAVE_METHOD => 'user'],
            ];
        }

        /**
         * @inheritdoc
         */
        protected function setUp(): void
        {
            $this->sessionName = 'frontEndSession';

            ini_set('session.use_only_cookies', '0');
            ini_set('session.name', $this->sessionName);

            $this->objectManager = Bootstrap::getObjectManager();

            /** @var SidResolverInterface $sidResolver */
            $this->appState = $this->getMockBuilder(State::class)
                ->setMethods(['getAreaCode'])
                ->disableOriginalConstructor()
                ->getMock();

            /** @var SidResolver $sidResolver */
            $this->sidResolver = $this->objectManager->create(
                SidResolver::class,
                [
                    'appState' => $this->appState,
                ]
            );

            $this->request = $this->objectManager->get(RequestInterface::class);
        }

        protected function tearDown(): void
        {
            global $mockPHPFunctions;
            $mockPHPFunctions = false;
            self::$isIniSetInvoked = [];
            self::$isSessionSetSaveHandlerInvoked = false;
            if ($this->model !== null) {
                $this->model->destroy();
                $this->model = null;
            }
        }
    }
}
