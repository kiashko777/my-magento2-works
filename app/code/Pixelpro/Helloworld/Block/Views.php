<?php

namespace Pixelpro\Helloworld\Block;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Pixelpro\Helloworld\Model\PostFactory;

class Views extends Template
{
    protected $_postFactory;

    public function __construct(
        Context $context,
        PostFactory           $postFactory
    )
    {
        $this->_postFactory = $postFactory;
        parent::__construct($context);
    }

    public function getPostCollection()
    {
        $post = $this->_postFactory->create();
        return $post->getCollection();
    }
}
