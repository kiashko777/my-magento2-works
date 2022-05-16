<?php

namespace Pixelpro\Helloworld\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Pixelpro\Helloworld\Model\PostFactory;

class InstallData implements InstallDataInterface
{
    protected $_postFactory;

    public function __construct(PostFactory $postFactory)
    {
        $this->_postFactory = $postFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $data = [
            'title' => "Magento Modules",
            'content' => "This post is all about Magento modules.",

        ];
        $post = $this->_postFactory->create();
        $post->addData($data)->save();
    }
}
