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

use Kinspeed\Stores\Model\Stores;

class MassDelete extends MassAction
{
    /**
     * @param Stores $store
     * @return $this
     */
    public function massAction(Stores $store)
    {
        $this->storeRepository->delete($store);
        return $this;
    }
}
