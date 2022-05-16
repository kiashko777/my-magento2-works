<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Locate all payment methods in the system and verify declaration of their blocks
 */

namespace Magento\Test\Integrity\Magento\Payment;

use Exception;
use FilesystemIterator;
use Magento\Framework\App\State;
use Magento\Framework\View\Element\BlockFactory;
use Magento\Framework\View\Element\Template;
use Magento\Payment\Helper\Data;
use Magento\Payment\Model\Info;
use Magento\Payment\Model\Method\Substitution;
use Magento\Payment\Model\MethodInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MethodsTest extends TestCase
{
    /**
     * @param string $methodClass
     * @param string $code
     * @dataProvider paymentMethodDataProvider
     * @magentoAppArea frontend
     * @throws Exception on various assertion failures
     */
    public function testPaymentMethod($code, $methodClass)
    {
        if ($code == 'vault') {
            return;
        }
        Bootstrap::getObjectManager()->configure($this->getTestConfiguration());
        /** @var $blockFactory BlockFactory */
        $blockFactory = Bootstrap::getObjectManager()->get(
            BlockFactory::class
        );
        $storeId = Bootstrap::getObjectManager()->get(
            StoreManagerInterface::class
        )->getStore()->getId();
        /** @var $model MethodInterface */
        if (empty($methodClass)) {
            /**
             * Note that $code is not whatever the payment method getCode() returns
             */
            $this->fail("Model of '{$code}' payment method is not found.");
        }
        $model = Bootstrap::getObjectManager()->create($methodClass);
        if ($code == Substitution::CODE) {
            $paymentInfo = $this->getMockBuilder(
                Info::class
            )->disableOriginalConstructor()->setMethods(
                []
            )->getMock();
            $paymentInfo->expects(
                $this->any()
            )->method(
                'getAdditionalInformation'
            )->willReturn(
                'Additional data mock'
            );
            $model->setInfoInstance($paymentInfo);
        }
        Bootstrap::getObjectManager()->get(State::class)
            ->setMode(State::MODE_DEVELOPER);
        $this->assertNotEmpty($model->getTitle());
        foreach ([$model->getFormBlockType(), $model->getInfoBlockType()] as $blockClass) {
            $message = "Block class: {$blockClass}";
            /** @var $block Template */
            $block = $blockFactory->createBlock($blockClass);
            $block->setArea('frontend');
            $this->assertFileExists((string)$block->getTemplateFile(), $message);
            if ($model->canUseInternal()) {
                try {
                    Bootstrap::getObjectManager()->get(
                        StoreManagerInterface::class
                    )->getStore()->setId(
                        Store::DEFAULT_STORE_ID
                    );
                    $block->setArea('Adminhtml');
                    $this->assertFileExists((string)$block->getTemplateFile(), $message);
                    Bootstrap::getObjectManager()->get(
                        StoreManagerInterface::class
                    )->getStore()->setId(
                        $storeId
                    );
                } catch (Exception $e) {
                    Bootstrap::getObjectManager()->get(
                        StoreManagerInterface::class
                    )->getStore()->setId(
                        $storeId
                    );
                    throw $e;
                }
            }
        }
        Bootstrap::getObjectManager()->get(State::class)
            ->setMode(State::MODE_DEFAULT);
    }

    /**
     * @return array
     */
    private function getTestConfiguration()
    {
        $result = [];
        $ds = DIRECTORY_SEPARATOR;
        $path = __DIR__ . $ds . str_repeat('..' . $ds, 5) . 'Magento';

        foreach ($this->collectFiles($path) as $file) {
            $config = include $file->getPathname();
            $result = array_replace_recursive($result, $config);
        }

        return $result;
    }

    /**
     * @param string $path
     * @return RegexIterator
     */
    private function collectFiles($path)
    {
        $ds = preg_quote(DIRECTORY_SEPARATOR);
        $flags = FilesystemIterator::CURRENT_AS_FILEINFO
            | FilesystemIterator::SKIP_DOTS;
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, $flags));

        return new RegexIterator(
            $iterator,
            '#' . $ds . 'etc' . $ds . 'di\.php$#',
            RegexIterator::MATCH,
            RegexIterator::USE_KEY
        );
    }

    /**
     * @return array
     */
    public function paymentMethodDataProvider()
    {
        /** @var $helper Data */
        $helper = Bootstrap::getObjectManager()->get(Data::class);
        $result = [];
        foreach ($helper->getPaymentMethods() as $code => $method) {
            $result[] = [$code, $method['model']];
        }
        return $result;
    }
}
