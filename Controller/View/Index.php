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

namespace Kinspeed\Stores\Controller\View;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Kinspeed\Stores\Model\ResourceModel\Stores\CollectionFactory as StoresCollectionFactory;
use Kinspeed\Stores\Model\Stores;
use Magento\Store\Model\StoreManagerInterface;
use Kinspeed\Stores\Block\Stores;

/**
 * Class Index
 * @package Kinspeed\Stores\Controller\View
 */
class Index extends Action
{
    /**
     * @var string
     */
    const META_DESCRIPTION_CONFIG_PATH = 'kinspeed_stores/store_content/meta_description';

    /**
     * @var string
     */
    const META_KEYWORDS_CONFIG_PATH = 'kinspeed_stores/store_content/meta_keywords';

    /**
     * @var string
     */
    const META_TITLE_CONFIG_PATH = 'kinspeed_stores/store_content/meta_title';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    public $storeManager;

    /** @var \Magento\Framework\View\Result\PageFactory  */
    public $resultPageFactory;

    /**
     * @var StoresCollectionFactory
     */
    public $storesCollectionFactory;

    /**
     * Configuration
     *
     * @var Stores
     */
    protected $storesConfig;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ScopeConfigInterface $scopeConfig,
        StoresCollectionFactory $storesCollectionFactory,
        StoreManagerInterface $storeManager,
        Stores $storesConfig
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->scopeConfig = $scopeConfig;
        $this->storesCollectionFactory = $storesCollectionFactory;
        $this->storeManager = $storeManager;
        $this->storesConfig = $storesConfig;
    }

    /**
     * Load the page defined in view/frontend/layout/stores_index_index.xml
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $url = $this->_url->getCurrentUrl();
        $moduleUrl = $this->storesConfig->getModuleUrlSettings();

        preg_match('/'.$moduleUrl.'\/(.*)/', $url, $matches);

        $details = $this->getStoreDetails($matches[1]);
        $allStores = $this->getAllStoreStores();

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getLayout()->getBlock('stores.stores.individual')->setDetails($details);
        $resultPage->getLayout()->getBlock('stores.stores.individual')->setAllStores($allStores );

        $resultPage->getConfig()->getTitle()->set(
            $this->scopeConfig->getValue(self::META_TITLE_CONFIG_PATH, ScopeInterface::SCOPE_STORE)
        );
        $resultPage->getConfig()->setDescription(
            $this->scopeConfig->getValue(self::META_DESCRIPTION_CONFIG_PATH, ScopeInterface::SCOPE_STORE)
        );
        $resultPage->getConfig()->setKeywords(
            $this->scopeConfig->getValue(self::META_KEYWORDS_CONFIG_PATH, ScopeInterface::SCOPE_STORE)
        );

        return $resultPage;

    }

    /**
     * return data from the loaded store details. Only the first store is returned if there are multiple urls
     *
     * @return array
     */
    public function getStoreDetails($url)
    {
        $collection = $this->getIndividualStore($url);
        foreach($collection as $store){
            return $store->getData();
        }
    }

    /**
     * return data from the loaded store details. Only the first store is returned if there are multiple urls
     *
     * @return array
     */
    public function getAllStoreStores()
    {
        $collection = $this->getAllStoresCollection();
        $data = [];
        foreach($collection as $store){
            $data[] = $store->getData();
        }
        return $data;
    }

    /**
     * return stores collection filtered by url
     *
     * @return CollectionFactory
     */
    public function getIndividualStore($url)
    {
        $collection = $this->storesCollectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('status', Stores::STATUS_ENABLED)
            ->addFieldToFilter('link', $url)
            ->addStoreFilter($this->storeManager->getStore()->getId())
            ->setOrder('name', 'ASC');
        return $collection;
    }

    /**
     * return stores collection 
     *
     * @return CollectionFactory
     */
    public function getAllStoresCollection()
    {
        $collection = $this->storesCollectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('status', Stores::STATUS_ENABLED)
            ->addStoreFilter($this->storeManager->getStore()->getId())
            ->setOrder('name', 'ASC');
        return $collection;
    }

}
