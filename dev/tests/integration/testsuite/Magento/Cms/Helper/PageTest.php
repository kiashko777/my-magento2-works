<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Cms\Helper;

use Magento\Customer\Model\Context;
use Magento\Framework\App\State;
use Magento\Framework\App\Test\Unit\Action\Stub\ActionStub;
use Magento\Framework\View\DesignInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\Request;
use Magento\TestFramework\Response;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea frontend
 */
class PageTest extends TestCase
{
    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Cms/_files/pages.php
     */
    public function testRenderPage()
    {
        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        $httpContext = $objectManager->get(\Magento\Framework\App\Http\Context::class);
        $httpContext->setValue(Context::CONTEXT_AUTH, false, false);
        $objectManager->get(State::class)->setAreaCode('frontend');
        $arguments = [
            'request' => $objectManager->get(Request::class),
            'response' => $objectManager->get(Response::class),
        ];
        $context = Bootstrap::getObjectManager()->create(
            \Magento\Framework\App\Action\Context::class,
            $arguments
        );
        $page = Bootstrap::getObjectManager()->get(\Magento\Cms\Model\Page::class);
        $page->load('page_design_blank', 'identifier');
        // fixture
        /** @var $pageHelper Page */
        $pageHelper = Bootstrap::getObjectManager()->get(Page::class);
        $result = $pageHelper->prepareResultPage(
            Bootstrap::getObjectManager()->create(
                ActionStub::class,
                ['context' => $context]
            ),
            $page->getId()
        );
        $design = Bootstrap::getObjectManager()->get(
            DesignInterface::class
        );
        $this->assertEquals('Magento/blank', $design->getDesignTheme()->getThemePath());
        $this->assertInstanceOf(\Magento\Framework\View\Result\Page::class, $result);
    }
}
