<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test\TestCase;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Message\Collection;
use Magento\Framework\Message\Error;
use Magento\Framework\Message\Manager;
use Magento\Framework\Message\MessageInterface;
use Magento\Framework\Message\Notice;
use Magento\Framework\Message\Success;
use Magento\Framework\Message\Warning;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\View\Element\Message\InterpretationStrategyInterface;
use Magento\TestFramework\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\Request;
use Magento\TestFramework\Response;
use Magento\TestFramework\TestCase\AbstractController;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionMethod;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ControllerAbstractTest extends AbstractController
{
    protected $_bootstrap;

    /** @var MockObject | Manager */
    private $messageManager;

    /** @var MockObject | InterpretationStrategyInterface */
    private $interpretationStrategyMock;

    /** @var MockObject | CookieManagerInterface */
    private $cookieManagerMock;

    /**
     * @var MockObject|Json
     */
    private $serializerMock;

    public function testGetRequest()
    {
        $request = $this->getRequest();
        $this->assertInstanceOf(Request::class, $request);
    }

    public function testGetResponse()
    {
        $response = $this->getResponse();
        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function testAssert404NotFound()
    {
        $this->getRequest()->setControllerName('noroute');
        $this->getResponse()->setBody(
            '404 Not Found test <h3>We are sorry, but the page you are looking for cannot be found.</h3>'
        );
        $this->assert404NotFound();

        $this->getResponse()->setBody('');
        try {
            $this->assert404NotFound();
        } catch (AssertionFailedError $e) {
            return;
        }
        $this->fail('Failed response body validation');
    }

    /**
     */
    public function testAssertRedirectFailure()
    {
        $this->expectException(AssertionFailedError::class);

        $this->assertRedirect();
    }

    /**
     * @depends testAssertRedirectFailure
     */
    public function testAssertRedirect()
    {
        /*
         * Prevent calling \Magento\Framework\App\Response\Http::setRedirect() because it dispatches event,
         * which requires fully initialized application environment intentionally not available
         * for unit tests
         */
        $setRedirectMethod = new ReflectionMethod(Http::class, 'setRedirect');
        $setRedirectMethod->invoke($this->getResponse(), 'http://magentocommerce.com');
        $this->assertRedirect();
        $this->assertRedirect($this->equalTo('http://magentocommerce.com'));
    }

    /**
     * @param array $expectedMessages
     * @param string|null $messageTypeFilter
     * @dataProvider assertSessionMessagesDataProvider
     */
    public function testAssertSessionMessagesSuccess(array $expectedMessages, $messageTypeFilter)
    {
        $this->addSessionMessages();
        /** @var MockObject|Constraint $constraint */
        $constraint =
            $this->createPartialMock(Constraint::class, ['toString', 'matches']);
        $constraint->expects(
            $this->once()
        )->method('matches')
            ->with($expectedMessages)
            ->willReturn(true);
        $this->assertSessionMessages($constraint, $messageTypeFilter);
    }

    private function addSessionMessages()
    {
        // emulate session messages
        $messagesCollection = new Collection();
        $messagesCollection
            ->addMessage(new Warning('some_warning'))
            ->addMessage(new Error('error_one'))
            ->addMessage(new Error('error_two'))
            ->addMessage(new Notice('some_notice'))
            ->addMessage(new Success('success!'));
        $this->messageManager->expects($this->any())->method('getMessages')
            ->willReturn($messagesCollection);

        $cookieMessages = [
            [
                'type' => 'warning',
                'text' => 'warning_cookie',
            ],
            [
                'type' => 'notice',
                'text' => 'notice_cookie',
            ],
            [
                'type' => 'success',
                'text' => 'success_cookie',
            ],
            [
                'type' => 'error',
                'text' => 'error_cookie',
            ],
        ];

        $this->cookieManagerMock->expects($this->any())
            ->method('getCookie')
            ->willReturn(json_encode($cookieMessages));
    }

    public function assertSessionMessagesDataProvider()
    {
        return [
            'message warning type filtering' => [
                ['some_warning', 'warning_cookie'],
                MessageInterface::TYPE_WARNING,
            ],
            'message error type filtering' => [
                ['error_one', 'error_two', 'error_cookie'],
                MessageInterface::TYPE_ERROR,
            ],
            'message notice type filtering' => [
                ['some_notice', 'notice_cookie'],
                MessageInterface::TYPE_NOTICE,
            ],
            'message success type filtering' => [
                ['success!', 'success_cookie'],
                MessageInterface::TYPE_SUCCESS,
            ],
        ];
    }

    public function testAssertSessionMessagesAll()
    {
        $this->addSessionMessages();

        $this->assertSessionMessages(
            $this->equalTo(
                [
                    'some_warning',
                    'error_one',
                    'error_two',
                    'some_notice',
                    'success!',
                    'warning_cookie',
                    'notice_cookie',
                    'success_cookie',
                    'error_cookie',
                ]
            )
        );
    }

    public function testAssertSessionMessagesEmpty()
    {
        $messagesCollection = new Collection();
        $this->messageManager->expects($this->any())->method('getMessages')
            ->willReturn($messagesCollection);

        $this->assertSessionMessages($this->isEmpty());
    }

    protected function setUp(): void
    {
        $testObjectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->messageManager = $this->createMock(Manager::class);
        $this->cookieManagerMock = $this->getMockForAbstractClass(CookieManagerInterface::class);
        $this->serializerMock = $this->getMockBuilder(Json::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->serializerMock->expects($this->any())->method('unserialize')->willReturnCallback(
            function ($serializedData) {
                return json_decode($serializedData, true);
            }
        );
        $this->interpretationStrategyMock = $this->getMockForAbstractClass(InterpretationStrategyInterface::class);
        $this->interpretationStrategyMock->expects($this->any())
            ->method('interpret')
            ->willReturnCallback(
                function (MessageInterface $message) {
                    return $message->getText();
                }
            );

        $request = $testObjectManager->getObject(Request::class);
        $response = $testObjectManager->getObject(Response::class);
        $this->_objectManager =
            $this->createPartialMock(ObjectManager::class, ['get', 'create']);
        $this->_objectManager->expects($this->any())
            ->method('get')
            ->willReturnMap(

                [
                    [RequestInterface::class, $request],
                    [ResponseInterface::class, $response],
                    [Manager::class, $this->messageManager],
                    [CookieManagerInterface::class, $this->cookieManagerMock],
                    [Json::class, $this->serializerMock],
                    [InterpretationStrategyInterface::class, $this->interpretationStrategyMock],
                ]

            );
    }

    /**
     * Bootstrap instance getter.
     * Mocking real bootstrap
     *
     * @return Bootstrap
     */
    protected function _getBootstrap()
    {
        if (!$this->_bootstrap) {
            $this->_bootstrap = $this->createPartialMock(Bootstrap::class, ['getAllOptions']);
        }
        return $this->_bootstrap;
    }
}
