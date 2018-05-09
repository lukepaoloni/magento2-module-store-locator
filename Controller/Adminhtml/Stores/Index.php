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

use \Kinspeed\Stores\Controller\Adminhtml\Stores as StoreController;

class Index extends StoreController
{
    /**
     * Stores list.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Kinspeed_Stores::stores');
        $resultPage->getConfig()->getTitle()->prepend(__('Stores'));
        $resultPage->addBreadcrumb(__('Stores'), __('Stores'));
        return $resultPage;
    }
}
