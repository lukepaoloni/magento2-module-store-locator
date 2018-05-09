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

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Framework\View\Result\PageFactory;
use Kinspeed\Stores\Api\StoreRepositoryInterface;
use Kinspeed\Stores\Api\Data\StoreInterface;
use Kinspeed\Stores\Api\Data\StoreInterfaceFactory;
use Kinspeed\Stores\Controller\Adminhtml\Stores as StoreController;
use Kinspeed\Stores\Model\Stores;
use Kinspeed\Stores\Model\ResourceModel\Stores as StoreResourceModel;

class InlineEdit extends StoreController
{
    /**
     * @var DataObjectHelper
     */
    public $dataObjectHelper;
    /**
     * @var DataObjectProcessor
     */
    public $dataObjectProcessor;
    /**
     * @var JsonFactory
     */
    public $jsonFactory;
    /**
     * @var StoreResourceModel
     */
    public $storeResourceModel;

    /**
     * @param Registry $registry
     * @param StoreRepositoryInterface $storeRepository
     * @param PageFactory $resultPageFactory
     * @param Date $dateFilter
     * @param Context $context
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param JsonFactory $jsonFactory
     * @param StoreResourceModel $storeResourceModel
     */
    public function __construct(
        Registry $registry,
        StoreRepositoryInterface $storeRepository,
        PageFactory $resultPageFactory,
        Date $dateFilter,
        Context $context,
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper,
        JsonFactory $jsonFactory,
        StoreResourceModel $storeResourceModel
    ) {
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper    = $dataObjectHelper;
        $this->jsonFactory         = $jsonFactory;
        $this->storeResourceModel = $storeResourceModel;
        parent::__construct($registry, $storeRepository, $resultPageFactory, $dateFilter, $context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        foreach (array_keys($postItems) as $storeId) {
            /** @var \Kinspeed\Stores\Model\Stores|StoreInterface $store */
            $store = $this->storeRepository->getById((int)$storeId);
            try {
                $storeData = $this->filterData($postItems[$storeId]);
                $this->dataObjectHelper->populateWithArray($store, $storeData , StoreInterface::class);
                $this->storeResourceModel->saveAttribute($store, array_keys($storeData));
            } catch (LocalizedException $e) {
                $messages[] = $this->getErrorWithStoreId($store, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithStoreId($store, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithStoreId(
                    $store,
                    __('Something went wrong while saving the store.')
                );
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add store id to error message
     *
     * @param Stores $store
     * @param string $errorText
     * @return string
     */
    public function getErrorWithStoreId(Stores $store, $errorText)
    {
        return '[Store ID: ' . $store->getId() . '] ' . $errorText;
    }
}
