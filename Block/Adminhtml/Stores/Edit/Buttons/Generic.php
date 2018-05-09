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
namespace Kinspeed\Stores\Block\Adminhtml\Stores\Edit\Buttons;

use Magento\Backend\Block\Widget\Context;
use Kinspeed\Stores\Api\StoreRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Generic
{
    /**
     * @var Context
     */
    public $context;

    /**
     * @var StoreRepositoryInterface
     */
    public $storeRepository;

    /**
     * @param Context $context
     * @param StoreRepositoryInterface $storeRepository
     */
    public function __construct(
        Context $context,
        StoreRepositoryInterface $storeRepository
    ) {
        $this->context = $context;
        $this->storeRepository = $storeRepository;
    }

    /**
     * Return Store page ID
     *
     * @return int|null
     */
    public function getStoreId()
    {
        try {
            return $this->storeRepository->getById(
                $this->context->getRequest()->getParam('store_id')
            )->getId();
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
