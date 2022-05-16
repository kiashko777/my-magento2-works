<?php

namespace Devall\Customform\Controller\Index;

use Devall\Customform\Model\Config;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 * @package Devall\Customform\Controller\Index
 */
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    private $config;

    /**
     * Constructor
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Config $config
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Config $config
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->config = $config;
        parent::__construct($context);
    }

    /**
     * Execute view action
     * @return ResultInterface
     */
    public function execute()
    {
        if ($this->config->isEnabled()) {
            return $this->resultPageFactory->create();
        }
        echo "<H1>Module not enabled!</H1>";

    }
}
