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
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Ui\Component\MassAction\Filter;
use Kinspeed\Stores\Api\StoreRepositoryInterface;
use Kinspeed\Stores\Api\Data\StoreInterface;
use Kinspeed\Stores\Api\Data\StoreInterfaceFactory;
use Kinspeed\Stores\Controller\Adminhtml\Stores;
use Kinspeed\Stores\Model\Uploader;
use Kinspeed\Stores\Model\UploaderPool;
use Kinspeed\Stores\Model\ResourceModel\Stores\CollectionFactory;



class Export extends Stores
{
    /**
     * @var DataObjectProcessor
     */
    public $dataObjectProcessor;

    /**
     * @var CollectionFactory
     */
    public $collectionFactory;

    /**
     * @var DataObjectHelper
     */
    public $dataObjectHelper;

    /**
     * @var UploaderPool
     */
    public $uploaderPool;
    
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    public $fileFactory;

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
        FileFactory $fileFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->fileFactory = $fileFactory;
        $this->storeFactory = $storeFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->uploaderPool = $uploaderPool;
        parent::__construct($registry, $storeRepository, $resultPageFactory, $dateFilter, $context);
    }
    
    /**
     * Export data grid to CSV format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        
        try {
            
            $content = '';
            $content .= '"store_id",';
            $content .= '"name",';
            $content .= '"address",';
            $content .= '"city",';
            $content .= '"country",';
            $content .= '"postcode",';
            $content .= '"region",';
            $content .= '"email",';
            $content .= '"phone",';
            $content .= '"link",';
            $content .= '"image",';
            $content .= '"latitude",';
            $content .= '"longitude",';
            $content .= '"status",';
            $content .= '"updated_at",';
            $content .= '"created_at",';
            $content .= '"schedule",';
            $content .= '"station",';
            $content .= '"description",';
            $content .= '"intro",';
            $content .= '"details_image",';
            $content .= '"distance",';
            $content .= '"external_link"';
            $content .= "\n";

            $fileName = 'stores_export.csv';
            $collection = $this->collectionFactory->create()->getData();
            
            foreach ($collection as $store) {
                array_shift($store); //skip the id
                $content .= implode(",", array_map([$this, 'addQuotationMarks'],$store));
                $content .= "\n";
            }

            return $this->fileFactory->create(
                $fileName,
                $content,
                DirectoryList::VAR_DIR
            );
            
            $this->messageManager->addSuccessMessage(__('You exported the file. It can be found in var folder or in browser downloads.'));
            $resultRedirect->setPath('stores/stores');
            
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('There was a problem exporting the data'));
            $resultRedirect->setPath('stores/stores/export');
        }
        
        return $resultRedirect;

    }
    
     /**
     * Add quotes to fields
     * @param string
     * @return string
     */
    public function addQuotationMarks($row)
    {
        return sprintf('"%s"', $row);
    }
}
