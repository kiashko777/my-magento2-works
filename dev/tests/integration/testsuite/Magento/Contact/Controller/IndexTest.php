<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Contact\Controller;

use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\TestCase\AbstractController;

/**
 * Contact index controller test
 */
class IndexTest extends AbstractController
{
    /**
     * @return array
     */
    public static function dataInvalidPostAction()
    {
        return [
            'missing_comment' => [
                'params' => [
                    'name' => 'customer name',
                    'comment' => '',
                    'email' => 'user@example.com',
                    'hideit' => '',
                ],
                'expectedMessage' => "Enter the comment and try again.",
            ],
            'missing_name' => [
                'params' => [
                    'name' => '',
                    'comment' => 'customer comment',
                    'email' => 'user@example.com',
                    'hideit' => '',
                ],
                'expectedMessage' => "Enter the Name and try again.",
            ],
            'invalid_email' => [
                'params' => [
                    'name' => 'customer name',
                    'comment' => 'customer comment',
                    'email' => 'invalidemail',
                    'hideit' => '',
                ],
                'expectedMessage' => "The email address is invalid. Verify the email address and try again.",
            ],
        ];
    }

    /**
     * Test contacting.
     */
    public function testPostAction()
    {
        $params = [
            'name' => 'customer name',
            'comment' => 'comment',
            'email' => 'user@example.com',
            'hideit' => '',
        ];
        $this->getRequest()->setPostValue($params)->setMethod(HttpRequest::METHOD_POST);

        $this->dispatch('contact/index/post');
        $this->assertRedirect($this->stringContains('contact/index'));
        $this->assertSessionMessages(
            $this->containsEqual(
                "Thanks for contacting us with your comments and questions. We&#039;ll respond to you very soon."
            ),
            MessageInterface::TYPE_SUCCESS
        );
    }

    /**
     * Test validation.
     *
     * @param array $params For Request.
     * @param string $expectedMessage Expected response.
     *
     * @dataProvider dataInvalidPostAction
     */
    public function testInvalidPostAction($params, $expectedMessage)
    {
        $this->getRequest()->setPostValue($params)->setMethod(HttpRequest::METHOD_POST);

        $this->dispatch('contact/index/post');
        $this->assertRedirect($this->stringContains('contact/index'));
        $this->assertSessionMessages(
            $this->containsEqual($expectedMessage),
            MessageInterface::TYPE_ERROR
        );
    }
}
