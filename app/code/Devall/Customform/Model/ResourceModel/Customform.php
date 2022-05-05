<?php

namespace Devall\Customform\Model\ResourceModel;

class Customform extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('devall_customform', 'id');
    }
}
