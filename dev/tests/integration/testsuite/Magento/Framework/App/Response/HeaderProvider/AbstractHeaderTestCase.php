<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\App\Response\HeaderProvider;

use ArrayIterator;
use Laminas\Http\Header\HeaderInterface;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\TestFramework\TestCase\AbstractController;

/**
 * Class AbstractHeaderTestCase
 */
abstract class AbstractHeaderTestCase extends AbstractController
{
    /**
     * @var HttpResponse
     */
    private $interceptedResponse;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->_objectManager->configure(
            [
                'preferences' => [
                    HttpResponse::class => 'Magento\Framework\App\Response\Http\Interceptor'
                ]
            ]
        );
        $this->interceptedResponse = $this->_objectManager->create(HttpResponse::class);
    }

    /**
     * Verify that a given header matches a given value
     *
     * @param string $name
     * @param string $value
     */
    protected function assertHeaderPresent($name, $value)
    {
        $value = [$value];
        $this->interceptedResponse->sendResponse();
        $header = $this->interceptedResponse->getHeader($name);

        $headerContent = [];
        if ($header instanceof ArrayIterator) {
            foreach ($header as $item) {
                $headerContent[] = $item->getFieldValue();
            }
        } elseif ($header instanceof HeaderInterface) {
            $headerContent[] = $header->getFieldValue();
        }

        $this->assertSame(
            $value,
            $headerContent
        );
    }

    /**
     * Assert is no header.
     *
     * @param string $name
     */
    protected function assertHeaderNotPresent($name)
    {
        $this->interceptedResponse->sendResponse();
        $this->assertFalse($this->interceptedResponse->getHeader($name));
    }
}
