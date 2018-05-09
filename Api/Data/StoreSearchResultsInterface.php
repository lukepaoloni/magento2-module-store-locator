<?php
/**
 * Kinspeed_Stores extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  Kinspeed
 * @package   Kinspeed_Stores
 * @copyright 2016 Claudiu Creanga
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @author    Claudiu Creanga
 */
namespace Kinspeed\Stores\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @api
 */
interface StoreSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get store list.
     *
     * @return \Kinspeed\Stores\Api\Data\StoreInterface[]
     */
    public function getItems();

    /**
     * Set stores list.
     *
     * @param \Kinspeed\Stores\Api\Data\StoreInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
