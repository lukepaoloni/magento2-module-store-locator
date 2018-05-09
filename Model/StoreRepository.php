<?php
declare(strict_types=1);
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
namespace Kinspeed\Stores\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Kinspeed\Stores\Api\StoreRepositoryInterface;
use Kinspeed\Stores\Api\Data;
use Kinspeed\Stores\Api\Data\StoreInterface;
use Kinspeed\Stores\Api\Data\StoreInterfaceFactory;
use Kinspeed\Stores\Api\Data\StoreSearchResultsInterfaceFactory;
use Kinspeed\Stores\Model\ResourceModel\Stores as ResourceStore;
use Kinspeed\Stores\Model\ResourceModel\Stores\Collection;
use Kinspeed\Stores\Model\ResourceModel\Stores\CollectionFactory as StoreCollectionFactory;

/**
 * Class StoreRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StoreRepository implements StoreRepositoryInterface
{
    /**
     * @var array
     */
    public $instances = [];
    /**
     * @var ResourceStore
     */
    public $resource;
    /**
     * @var StoreManagerInterface
     */
    public $storeManager;
    /**
     * @var StoreCollectionFactory
     */
    public $storeCollectionFactory;
    /**
     * @var StoreSearchResultsInterfaceFactory
     */
    public $searchResultsFactory;
    /**
     * @var StoreInterfaceFactory
     */
    public $storeInterfaceFactory;
    /**
     * @var DataObjectHelper
     */
    public $dataObjectHelper;

    public function __construct(
        ResourceStore $resource,
        StoreManagerInterface $storeManager,
        StoreCollectionFactory $storeCollectionFactory,
        StoreSearchResultsInterfaceFactory $storeSearchResultsInterfaceFactory,
        StoreInterfaceFactory $storeInterfaceFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->resource                 = $resource;
        $this->storeManager             = $storeManager;
        $this->storeCollectionFactory  = $storeCollectionFactory;
        $this->searchResultsFactory     = $storeSearchResultsInterfaceFactory;
        $this->storeInterfaceFactory   = $storeInterfaceFactory;
        $this->dataObjectHelper         = $dataObjectHelper;
    }
    /**
     * Save page.
     *
     * @param \Kinspeed\Stores\Api\Data\StoreInterface $store
     * @return \Kinspeed\Stores\Api\Data\StoreInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(StoreInterface $store)
    {
        /** @var StoreInterface|\Magento\Framework\Model\AbstractModel $store */
        if (empty($store->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $store->setStoreId($storeId);
        }
        try {
            $this->resource->save($store);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the store: %1',
                $exception->getMessage()
            ));
        }
        return $store;
    }

    /**
     * Retrieve Store.
     *
     * @param int $storeId
     * @return \Kinspeed\Stores\Api\Data\StoreInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($storeId)
    {
        if (!isset($this->instances[$storeId])) {

            /** @var \Kinspeed\Stores\Api\Data\StoreInterface|\Magento\Framework\Model\AbstractModel $store */
            $store = $this->storeInterfaceFactory->create();
            $this->resource->load($store, $storeId);
            
            if (!$store->getId()) {
                throw new NoSuchEntityException(__('Requested store doesn\'t exist'));

           }
            $this->instances[$storeId] = $store;
        }

        return $this->instances[$storeId];;
    }

    /**
     * Retrieve pages matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Kinspeed\Stores\Api\Data\StoreSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Kinspeed\Stores\Api\Data\StoreSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Kinspeed\Stores\Model\ResourceModel\Stores\Collection $collection */
        $collection = $this->storeCollectionFactory->create();

        //Add filters from root filter group to the collection
        /** @var FilterGroup $group */
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        $sortOrders = $searchCriteria->getSortOrders();
        /** @var SortOrder $sortOrder */
        if ($sortOrders) {
            foreach ($searchCriteria->getSortOrders() as $sortOrder) {
                $field = $sortOrder->getField();
                $collection->addOrder(
                    $field,
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        } else {
            // set a default sorting order since this method is used constantly in many
            // different blocks
            $field = 'store_id';
            $collection->addOrder($field, 'ASC');
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        /** @var \Kinspeed\Stores\Api\Data\StoreInterface[] $stores */
        $stores = [];
        /** @var \Kinspeed\Stores\Model\Stores $store */
        foreach ($collection as $store) {
            /** @var \Kinspeed\Stores\Api\Data\StoreInterface $storeDataObject */
            $storeDataObject = $this->storeInterfaceFactory->create();
            $this->dataObjectHelper->populateWithArray($storeDataObject, $store->getData(), StoreInterface::class);
            $stores[] = $storeDataObject;
        }
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->setItems($stores);
    }

    /**
     * Delete store.
     *
     * @param \Kinspeed\Stores\Api\Data\StoreInterface $store
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(StoreInterface $store)
    {
        /** @var \Kinspeed\Stores\Api\Data\StoreInterface|\Magento\Framework\Model\AbstractModel $store */
        $id = $store->getId();
        try {
            unset($this->instances[$id]);
            $this->resource->delete($store);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to remove store %1', $id)
            );
        }
        unset($this->instances[$id]);
        return true;
    }

    /**
     * Delete store by ID.
     *
     * @param int $storeId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($storeId)
    {
        $store = $this->getById($storeId);
        return $this->delete($store);
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection $collection
     * @return $this
     * @throws \Magento\Framework\Exception\InputException
     */
    public function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $collection)
    {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $fields[] = $filter->getField();
            $conditions[] = [$condition => $filter->getValue()];
        }
        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
        return $this;
    }

}
