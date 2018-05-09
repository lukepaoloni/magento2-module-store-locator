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
namespace Kinspeed\Stores\Controller\Adminhtml\Stores;

use Magento\Backend\Model\Session;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\File\Csv;
use Kinspeed\Stores\Api\StoreRepositoryInterface;
use Kinspeed\Stores\Api\Data\StoreInterface;
use Kinspeed\Stores\Api\Data\StoreInterfaceFactory;
use Kinspeed\Stores\Controller\Adminhtml\Stores;
use Magento\UrlRewrite\Model\UrlRewriteFactory;
use Kinspeed\Stores\Model\Uploader;
use Kinspeed\Stores\Model\UploaderPool;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\UrlRewrite as BaseUrlRewrite;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite as UrlRewriteService;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Kinspeed\Stores\Block\Stores;

class ImportFile extends Stores
{
    /**
     * @var DataObjectProcessor
     */
    public $dataObjectProcessor;

    /**
     * @var DataObjectHelper
     */
    public $dataObjectHelper;

    /**
     * @var UploaderPool
     */
    public $uploaderPool;
    
    /**
     * @var csvProcessor
     */
    public $csvProcessor;


    /**
     * @var BaseUrlRewrite
     */
    protected $urlRewrite;

    /**
     * Url rewrite service
     *
     * @var $urlRewriteService
     */
    protected $urlRewriteService;

    /**
     * Url finder
     *
     * @var UrlFinderInterface
     */
    protected $urlFinder;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /** @var UrlRewriteFactory */
    protected $urlRewriteFactory;

    /**
     * Configuration
     *
     * @var Stores
     */
    protected $storesConfig;

    /**
     * StoreInterfaceFactory
     *
     * @var Stores
     */
    protected $storeFactory;

    /**

     */
    public function __construct(
        Registry $registry,
        StoreRepositoryInterface $storeRepository,
        PageFactory $resultPageFactory,
        Date $dateFilter,
        Context $context,
        StoreInterfaceFactory $storeFactory,
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper,
        UploaderPool $uploaderPool,
        BaseUrlRewrite $urlRewrite,
        UrlRewriteService $urlRewriteService,
        UrlFinderInterface $urlFinder,
        StoreManagerInterface $storeManager,
        Stores $storesConfig,
        UrlRewriteFactory $urlRewriteFactory,
        Csv $csvProcessor
    ) {
        $this->csvProcessor = $csvProcessor;
        $this->urlRewrite = $urlRewrite;
        $this->urlRewriteService = $urlRewriteService;
        $this->urlFinder = $urlFinder;
        $this->storeManager = $storeManager;
        $this->storesConfig = $storesConfig;
        $this->storeFactory = $storeFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->uploaderPool = $uploaderPool;
        $this->urlRewriteFactory = $urlRewriteFactory;
        parent::__construct($registry, $storeRepository, $resultPageFactory, $dateFilter, $context);
    }

    /**
     * run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $store = null;
        $data = $this->getRequest()->getPostValue();
        $filePath = $data["import"][0]["path"].$data["import"][0]["file"];
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data["import"][0]["path"] && $data["import"][0]["file"]) {
            
            try {
                $rawStoreData = $this->csvProcessor->getData($filePath);
                
                // first row of file represents headers
                $fileHeaders = $rawStoreData[0];
                $processedStoreData = $this->filterFileData($fileHeaders, $rawStoreData);
            
                foreach($processedStoreData as $individualStore) {
                    
                    $storeId = !empty($individualStore['store_id']) ? $individualStore['store_id'] : null;

                    if ($storeId) {
                        $store = $this->storeRepository->getById((int)$storeId);
                    } else {
                        unset($individualStore['store_id']);
                        $store = $this->storeFactory->create();
                    }
                    $storeIds = $individualStore["store_id"] ?? $this->storeManager->getStore()->getId();

                    $this->dataObjectHelper->populateWithArray($store,$individualStore,StoreInterface::class);
                    $this->storeRepository->save($store);

                    if($individualStore["link"]){
                        $this->saveUrlRewrite($individualStore["link"], $store->getId(), $storeIds);
                    }

                }
    
                $this->messageManager->addSuccessMessage(__('Your file has been imported successfully'));
                
                $resultRedirect->setPath('stores/stores');                    
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                if ($store != null) {
                    $this->storeStoreDataToSession(
                        $this->dataObjectProcessor->buildOutputDataArray(
                            $store,
                            StoreInterface::class
                        )
                    );
                }
                $resultRedirect->setPath('stores/stores/edit');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('There was an error importing the file'));
                if ($store != null) {
                    $this->storeStoreDataToSession(
                        $this->dataObjectProcessor->buildOutputDataArray(
                            $store,
                            StoreInterface::class
                        )
                    );
                }
                $resultRedirect->setPath('stores/stores/import');
            }
                
        } else {
            $this->messageManager->addError(__('Please upload a file'));
        }

        return $resultRedirect;
    }

    /**
     * @param $storeData
     */
    public function storeStoreDataToSession($storeData)
    {
        $this->_getSession()->setKinspeedStoresStoresData($storeData);
    }

    /**
     * Filter data so that it will skip empty rows and headers
     *
     * @param   array $fileHeaders
     * @param   array $rawStoreData
     * @return  array
     */
    public function filterFileData(array $fileHeaders, array $rawStoreData)
    {
        $rowCount=0;
        $rawDataRows = [];
        
        foreach ($rawStoreData as $rowIndex => $dataRow) {
            
            // skip headers
            if ($rowIndex == 0) {
                continue;
            }
            // skip empty rows
            if (count($dataRow) <= 1) {
                unset($rawStoreData[$rowIndex]);
                continue;
            }
            /* we take rows from [0] = > value to [website] = base */
            if ($rowIndex > 0) {
                foreach ($dataRow as $rowIndex => $dataRowNew) {
                    $rawDataRows[$rowCount][$fileHeaders[$rowIndex]] = $dataRowNew;
                }
            }
            $rowCount++;
        }
        return $rawDataRows;
    }
    
    
    /**
     * @param $type
     * @return Uploader
     * @throws \Exception
     */
    public function getUploader($type)
    {
        return $this->uploaderPool->getUploader($type);
    }

    /**
     * Saves the url rewrite for that specific store
     * @param $link string
     * @param $id int
     * @param $storeIds string
     * @return void
     */
    public function saveUrlRewrite($link, $id, $storeIds)
    {
        $moduleUrl = $this->storesConfig->getModuleUrlSettings();
        $getCustomUrlRewrite = $moduleUrl . "/" . $link;
        $storeId = $moduleUrl . "-" . $id;

        $storeIds = explode(",", $storeIds);
        foreach ($storeIds as $storeId){

            $filterData = [
                UrlRewriteService::STORE_ID => $storeId,
                UrlRewriteService::REQUEST_PATH => $getCustomUrlRewrite,
                UrlRewriteService::ENTITY_ID => $id,

            ];

            // check if there is an entity with same url and same id
            $rewriteFinder = $this->urlFinder->findOneByData($filterData);

            // if there is then do nothing, otherwise proceed
            if ($rewriteFinder === null) {

                // check maybe there is an old url with this target path and delete it
                $filterDataOldUrl = [
                    UrlRewriteService::STORE_ID => $storeId,
                    UrlRewriteService::REQUEST_PATH => $getCustomUrlRewrite,
                ];
                $rewriteFinderOldUrl = $this->urlFinder->findOneByData($filterDataOldUrl);

                if ($rewriteFinderOldUrl !== null) {
                    $this->urlRewrite->load($rewriteFinderOldUrl->getUrlRewriteId())->delete();
                }

                // check maybe there is an old id with different url, in this case load the id and update the url
                $filterDataOldId = [
                    UrlRewriteService::STORE_ID => $storeId,
                    UrlRewriteService::ENTITY_TYPE => $storeId,
                    UrlRewriteService::ENTITY_ID => $id
                ];
                $rewriteFinderOldId = $this->urlFinder->findOneByData($filterDataOldId);

                if ($rewriteFinderOldId !== null) {
                    $this->urlRewriteFactory->create()->load($rewriteFinderOldId->getUrlRewriteId())
                        ->setRequestPath($getCustomUrlRewrite)
                        ->save();

                    continue;
                }

                // now we can save
                $this->urlRewriteFactory->create()
                    ->setStoreId($storeId)
                    ->setIdPath(rand(1, 100000))
                    ->setRequestPath($getCustomUrlRewrite)
                    ->setTargetPath("stores/view/index")
                    ->setEntityType($storeId)
                    ->setEntityId($id)
                    ->setIsAutogenerated(0)
                    ->save();
            }
        }
    }
    
}
