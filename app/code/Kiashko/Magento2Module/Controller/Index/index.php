<?php

namespace Kiashko\Magento2Module\Controller\Index;

use Magento\Framework\App\ActionInterface;

class index implements ActionInterface
{
    protected $resultFactory;

    public function __construct(\Magento\Framework\Controller\Result\RawFactory $resultFactory)
    {
        $this->resultFactory = $resultFactory;
    }

    public function execute()
    {
        return $this->resultFactory->create()->setContents("<h1>This is the first Magento 2 module created by Roman Kiashko!</h1>");
    }
}
