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

use Kinspeed\Stores\Controller\Adminhtml\Stores;
use Kinspeed\Stores\Controller\RegistryConstants;

class Edit extends Stores
{
    /**
     * Initialize current store and set it in the registry.
     *
     * @return int
     */
    public function _initStore()
    {
        $storeId = $this->getRequest()->getParam('store_id');
        $this->coreRegistry->register(RegistryConstants::CURRENT_STOCKIST_ID, $storeId);

        return $storeId;
    }

    /**
     * Edit or create store
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $storeId = $this->_initStore();
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Kinspeed_Stores::stores');
        $resultPage->getConfig()->getTitle()->prepend(__('Stores'));
        $resultPage->addBreadcrumb(__('Stores'), __('Stores'), $this->getUrl('stores/stores'));

        if ($storeId === null) {
            $resultPage->addBreadcrumb(__('New Store'), __('New Store'));
            $resultPage->getConfig()->getTitle()->prepend(__('New Store'));
        } else {
            $resultPage->addBreadcrumb(__('Edit Store'), __('Edit Store'));
            $resultPage->getConfig()->getTitle()->prepend(
                $this->storeRepository->getById($storeId)->getName()
            );
        }
        return $resultPage;
    }
}
