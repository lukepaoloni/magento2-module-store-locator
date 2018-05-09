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

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Kinspeed\Stores\Controller\Adminhtml\Stores;

class Delete extends Stores
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('store_id');
        if ($id) {
            try {
                $this->storeRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('The store has been deleted.'));
                $resultRedirect->setPath('stores/*/');
                return $resultRedirect;
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('The store no longer exists.'));
                return $resultRedirect->setPath('stores/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('stores/stores/edit', ['store_id' => $id]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('There was a problem deleting the store'));
                return $resultRedirect->setPath('stores/stores/edit', ['store_id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a store to delete.'));
        $resultRedirect->setPath('stores/*/');
        return $resultRedirect;
    }
}
