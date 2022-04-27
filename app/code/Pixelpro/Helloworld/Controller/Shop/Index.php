<?php

namespace Pixelpro\Helloworld\Controller\Shop;
class Index extends \Magento\Framework\App\Action\Action
{
    protected \Magento\Framework\View\Result\PageFactory $_pageFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context      $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory)
    {
        $this->_pageFactory = $pageFactory;
        return parent::__construct($context);
    }

    public function execute()
    {
        return $this->_pageFactory->create();
    }
}
