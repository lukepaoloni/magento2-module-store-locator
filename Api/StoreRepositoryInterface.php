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
namespace Kinspeed\Stores\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Kinspeed\Stores\Api\Data\StoreInterface;

/**
 * @api
 */
interface StoreRepositoryInterface
{
    /**
     * Save page.
     *
     * @param StoreInterface $store
     * @return StoreInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(StoreInterface $store);

    /**
     * Retrieve Store.
     *
     * @param int $storeId
     * @return StoreInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($storeId);

    /**
     * Retrieve pages matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Kinspeed\Stores\Api\Data\StoreSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete store.
     *
     * @param StoreInterface $store
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(StoreInterface $store);

    /**
     * Delete store by ID.
     *
     * @param int $storeId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($storeId);
}
