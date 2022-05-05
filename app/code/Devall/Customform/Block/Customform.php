<?php

namespace Devall\Customform\Block;

use Magento\Framework\View\Element\Template\Context;

/**
 * Customform content block
 */
class Customform extends \Magento\Framework\View\Element\Template
{
    /**
     * Index constructor.
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array   $data = []
    )
    {
        parent::__construct($context, $data);
    }

    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__("Roman's Custom Form"));

        return parent::_prepareLayout();
    }
}
