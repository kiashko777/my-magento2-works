<?php
declare(strict_types=1);

namespace Devall\Customform\Api\Data;

interface CustomformSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Customform list.
     * @return CustomformInterface[]
     */
    public function getItems();

    /**
     * Set id list.
     * @param CustomformInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
