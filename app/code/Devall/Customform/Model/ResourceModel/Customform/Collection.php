<?php


namespace Devall\Customform\Model\ResourceModel\Customform;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'id';
    protected $_previewFlag;

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Devall\Customform\Model\Customform::class,
            \Devall\Customform\Model\ResourceModel\Customform::class
        );
        $this->_map['fields']['id'] = 'main_table.id';
    }
}
