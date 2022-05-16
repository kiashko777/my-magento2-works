<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test\Integrity\Modular;

use DOMDocument;
use DOMNode;
use DOMXPath;
use Exception;
use Magento\CustomerSegment\Model\ResourceModel\Segment\Report\Detail\Collection;
use Magento\Framework\App\Utility\Files;
use Magento\Framework\Data\Argument\InterpreterInterface;
use Magento\Framework\View\Layout\Argument\Parser;
use Magento\Framework\View\Model\Layout\Merge;
use Magento\GroupedProduct\Model\ResourceModel\Product\Type\Grouped\AssociatedProductsCollection;
use Magento\Logging\Model\ResourceModel\Grid\Actions;
use Magento\Logging\Model\ResourceModel\Grid\ActionsGroup;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Wishlist\Model\ResourceModel\Item\Collection\Grid;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class LayoutFilesTest extends TestCase
{
    /**
     * @var Parser
     */
    protected $_argParser;

    /**
     * @var InterpreterInterface
     */
    protected $_argInterpreter;

    /**
     * @param string $area
     * @param string $layoutFile
     * @dataProvider layoutArgumentsDataProvider
     */
    public function testLayoutArguments($area, $layoutFile)
    {
        Bootstrap::getInstance()->loadArea($area);
        $dom = new DOMDocument();
        $dom->load($layoutFile);
        $xpath = new DOMXPath($dom);
        $argumentNodes = $xpath->query('/layout//arguments/argument | /layout//action/argument');
        /** @var DOMNode $argumentNode */
        foreach ($argumentNodes as $argumentNode) {
            try {
                $argumentData = $this->_argParser->parse($argumentNode);
                if ($this->isSkippedArgument($argumentData)) {
                    continue;
                }
                $this->_argInterpreter->evaluate($argumentData);
            } catch (Exception $e) {
                $this->fail($e->getMessage());
            }
        }
    }

    /**
     * Whether an argument should be skipped, because it cannot be evaluated in the testing environment
     *
     * @param array $argumentData
     * @return bool
     */
    protected function isSkippedArgument(array $argumentData)
    {
        // Do not take into account argument name, shared and parameters
        unset($argumentData['name'], $argumentData['param'], $argumentData['shared']);

        $isUpdater = isset($argumentData['updater']);
        unset($argumentData['updater']);

        // Arguments, evaluation of which causes a run-time error, because of unsafe assumptions to the environment
        $typeAttr = Merge::TYPE_ATTRIBUTE;
        $prCollection =
            AssociatedProductsCollection::class;
        $ignoredArguments = [
            [
                $typeAttr => 'object',
                'value' => $prCollection,
            ],
            [$typeAttr => 'object', 'value' => Grid::class],
            [
                $typeAttr => 'object',
                'value' => Collection::class
            ],
            [$typeAttr => 'options', 'model' => ActionsGroup::class],
            [$typeAttr => 'options', 'model' => Actions::class],
        ];
        $isIgnoredArgument = in_array($argumentData, $ignoredArguments, true);

        unset($argumentData[$typeAttr]);
        $hasValue = !empty($argumentData);

        return $isIgnoredArgument || $isUpdater && !$hasValue;
    }

    /**
     * @return array
     */
    public function layoutArgumentsDataProvider()
    {
        $areas = ['Adminhtml', 'frontend', 'email'];
        $data = [];
        foreach ($areas as $area) {
            $layoutFiles = Files::init()->getLayoutFiles(['area' => $area], false);
            foreach ($layoutFiles as $layoutFile) {
                $data[substr($layoutFile, strlen(BP))] = [$area, $layoutFile];
            }
        }
        return $data;
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->_argParser = $objectManager->get(Parser::class);
        $this->_argInterpreter = $objectManager->get('layoutArgumentGeneratorInterpreter');
    }
}
