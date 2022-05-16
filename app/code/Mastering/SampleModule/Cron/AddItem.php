<?php

namespace Mastering\SampleModule\Cron;

use Mastering\SampleModule\Model\Config;
use Mastering\SampleModule\Model\ItemFactory;


class AddItem
{
    private $itemFactory;
    private $config;

    public function _construct(ItemFactory $itemFactory, Config $config)
    {
        $this->itemFactory = $itemFactory;
        $this->config = $config;
    }

    public function execute()
    {
        if ($this->config->isEnabled()) {
            $this->itemFactory->create()
                ->setName('Scheduled item')
                ->setDescription('Created at' . time())
                ->save();
        }
    }

}
