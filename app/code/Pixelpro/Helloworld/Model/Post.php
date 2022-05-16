<?php

namespace Pixelpro\Helloworld\Model;
class Post extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'pixelpro_helloworld_post';
    protected $_cacheTag = 'pixelpro_helloworld_post';
    protected $_eventPrefix = 'pixelpro_helloworld_post';

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];
        return $values;
    }

    protected function _construct()
    {
        $this->_init('Pixelpro\Helloworld\Model\ResourceModel\Post');
    }
}
