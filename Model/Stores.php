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

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Data\Collection\Db;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filter\FilterManager;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Kinspeed\Stores\Api\Data\StoreInterface;
use Kinspeed\Stores\Model\Stores\Url;
use Kinspeed\Stores\Model\ResourceModel\Stores as StoreResourceModel;
use Kinspeed\Stores\Model\Routing\RoutableInterface;
use Kinspeed\Stores\Model\Source\AbstractSource;

/**
 * @method StoreResourceModel _getResource()
 * @method StoreResourceModel getResource()
 */
class Stores extends AbstractModel implements StoreInterface, RoutableInterface
{
    /**
     * @var int
     */
    const STATUS_ENABLED = 1;
    /**
     * @var int
     */
    const STATUS_DISABLED = 0;
    /**
     * @var Url
     */
    public $urlModel;
    /**
     * cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'kinspeed_stores';

    /**
     * cache tag
     *
     * @var string
     */
    public $_cacheTag = 'kinspeed_stores_stores';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    public $_eventPrefix = 'kinspeed_stores_stores';

    /**
     * filter model
     *
     * @var \Magento\Framework\Filter\FilterManager
     */
    public $filter;

    /**
     * @var UploaderPool
     */
    public $uploaderPool;

    /**
     * @var \Kinspeed\Stores\Model\Output
     */
    public $outputProcessor;

    /**
     * @var AbstractSource[]
     */
    public $optionProviders;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param Output $outputProcessor
     * @param UploaderPool $uploaderPool
     * @param FilterManager $filter
     * @param Url $urlModel
     * @param array $optionProviders
     * @param array $data
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Output $outputProcessor,
        UploaderPool $uploaderPool,
        FilterManager $filter,
        Url $urlModel,
        array $optionProviders = [],
        array $data = [],
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null
    ) {
        $this->outputProcessor = $outputProcessor;
        $this->uploaderPool    = $uploaderPool;
        $this->filter          = $filter;
        $this->urlModel        = $urlModel;
        $this->optionProviders = $optionProviders;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(StoreResourceModel::class);
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->getData(StoreInterface::TYPE);
    }

    /**
     * @param $storeId
     * @return StoreInterface
     */
    public function setStoreId($storeId)
    {
        $this->setData(StoreInterface::STORE_ID, $storeId);
        return $this;
    }
    
    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->getData(StoreInterface::COUNTRY);
    }

    /**
     * set name
     *
     * @param $name
     * @return StoreInterface
     */
    public function setName($name)
    {
        return $this->setData(StoreInterface::NAME, $name);
    }

    /**
     * set external link
     *
     * @param $external_link
     * @return StoreInterface
     */
    public function setExternalLink($external_link)
    {
        return $this->setData(StoreInterface::EXTERNAL_LINK, $external_link);
    }


    /**
     * set schedule
     *
     * @param $schedule
     * @return StoreInterface
     */
    public function setSchedule($schedule)
    {
        return $this->setData(StoreInterface::SCHEDULE, $schedule);
    }

    /**
     * set distance
     *
     * @param $distance
     * @return StoreInterface
     */
    public function setDistance($distance)
    {
        return $this->setData(StoreInterface::DISTANCE, $distance);
    }

    /**
     * set description
     *
     * @param $description
     * @return StoreInterface
     */
    public function setDescription($description)
    {
        return $this->setData(StoreInterface::DESCRIPTION, $description);
    }

    /**
     * set station
     *
     * @param $station
     * @return StoreInterface
     */
    public function setStation($station)
    {
        return $this->setData(StoreInterface::STATION, $station);
    }

    /**
     * set intro
     *
     * @param $intro
     * @return StoreInterface
     */
    public function setIntro($intro)
    {
        return $this->setData(StoreInterface::INTRO, $intro);
    }

    /**
     * set type
     *
     * @param $type
     * @return StoreInterface
     */
    public function setType($type)
    {
        return $this->setData(StoreInterface::TYPE, $type);
    }

    /**
     * Set country
     *
     * @param $country
     * @return StoreInterface
     */
    public function setCountry($country)
    {
        return $this->setData(StoreInterface::COUNTRY, $country);
    }
    
        /**
     * set link
     *
     * @param $link
     * @return StoreInterface
     */
    public function setLink($link)
    {
        return $this->setData(StoreInterface::LINK, $link);
    }

    /**
     * set address
     *
     * @param $address
     * @return StoreInterface
     */
    public function setAddress($address)
    {
        return $this->setData(StoreInterface::ADDRESS, $address);
    }

    /**
     * set city
     *
     * @param $city
     * @return StoreInterface
     */
    public function setCity($city)
    {
        return $this->setData(StoreInterface::CITY, $city);
    }

    /**
     * set postcode
     *
     * @param $postcode
     * @return StoreInterface
     */
    public function setPostcode($postcode)
    {
        return $this->setData(StoreInterface::POSTCODE, $postcode);
    }

    /**
     * set region
     *
     * @param $region
     * @return StoreInterface
     */
    public function setRegion($region)
    {
        return $this->setData(StoreInterface::REGION, $region);
    }

    /**
     * set email
     *
     * @param $email
     * @return StoreInterface
     */
    public function setEmail($email)
    {
        return $this->setData(StoreInterface::EMAIL, $email);
    }

    /**
     * set phone
     *
     * @param $phone
     * @return StoreInterface
     */
    public function setPhone($phone)
    {
        return $this->setData(StoreInterface::PHONE, $phone);
    }

    /**
     * set latitude
     *
     * @param $latitude
     * @return StoreInterface
     */
    public function setLatitude($latitude)
    {
        return $this->setData(StoreInterface::LATITUDE, $latitude);
    }
    
    /**
     * set longitude
     *
     * @param $longitude
     * @return StoreInterface
     */
    public function setLongitude($longitude)
    {
        return $this->setData(StoreInterface::LONGITUDE, $longitude);
    }

    /**
     * Set status
     *
     * @param $status
     * @return StoreInterface
     */
    public function setStatus($status)
    {
        return $this->setData(StoreInterface::STATUS, $status);
    }    
    
    /**
     * set image
     *
     * @param $image
     * @return StoreInterface
     */
    public function setImage($image)
    {
        return $this->setData(StoreInterface::IMAGE, $image);
    }

    /**
     * set created at
     *
     * @param $createdAt
     * @return StoreInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(StoreInterface::CREATED_AT, $createdAt);
    }

    /**
     * set updated at
     *
     * @param $updatedAt
     * @return StoreInterface
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(StoreInterface::UPDATED_AT, $updatedAt);
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getData(StoreInterface::NAME);
    }

    /**
     * Get url key
     *
     * @return string
     */
    public function getLink()
    {
        return $this->getData(StoreInterface::LINK);
    }
    
    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->getData(StoreInterface::ADDRESS);
    }

    /**
     * Get schedule
     *
     * @return string
     */
    public function getSchedule()
    {
        return $this->getData(StoreInterface::SCHEDULE);
    }

    /**
     * Get intro
     *
     * @return string
     */
    public function getIntro()
    {
        return $this->getData(StoreInterface::INTRO);
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->getData(StoreInterface::DESCRIPTION);
    }

    /**
     * Get station
     *
     * @return string
     */
    public function getStation()
    {
        return $this->getData(StoreInterface::STATION);
    }

    /**
     * Get distance
     *
     * @return string
     */
    public function getDistance()
    {
        return $this->getData(StoreInterface::DISTANCE);
    }

    /**
     * Get details image
     *
     * @return string
     */
    public function getDetailsImage()
    {
        return $this->getData(StoreInterface::DETAILS_IMAGE);
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->getData(StoreInterface::CITY);
    }
    
    /**
     * Get postcode
     *
     * @return string
     */
    public function getPostcode()
    {
        return $this->getData(StoreInterface::POSTCODE);
    }

    /**
     * Get region
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->getData(StoreInterface::REGION);
    }
    
    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->getData(StoreInterface::EMAIL);
    }
    
    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->getData(StoreInterface::IMAGE);
    }
    
    /**
     * @return bool|string
     * @throws LocalizedException
     */
    public function getImageUrl()
    {
        $url = false;
        $image = $this->getImage();
        if ($image) {
            if (is_string($image)) {
                $uploader = $this->uploaderPool->getUploader('image');
                $url = $uploader->getBaseUrl().$uploader->getBasePath().$image;
            } else {
                throw new LocalizedException(
                    __('Something went wrong while getting the image url.')
                );
            }
        }
        return $url;
    }

    /**
     * @return bool|string
     * @throws LocalizedException
     */
    public function getDetailsImageUrl()
    {
        $url = false;
        $image = $this->getDetailsImage();
        if ($image) {
            if (is_string($image)) {
                $uploader = $this->uploaderPool->getUploader('image');
                $url = $uploader->getBaseUrl().$uploader->getBasePath().$image;
            } else {
                throw new LocalizedException(
                    __('Something went wrong while getting the image url.')
                );
            }
        }
        return $url;
    }

    /**
     * Get external link
     *
     * @return string
     */
    public function getExternalLink()
    {
        return $this->getData(StoreInterface::EXTERNAL_LINK);
    }

    /**
     * set details image
     *
     * @param $details_image
     * @return StoreInterface
     */
    public function setDetailsImage($details_image)
    {
        return $this->setData(StoreInterface::DETAILS_IMAGE, $details_image);
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->getData(StoreInterface::PHONE);
    }
    
    /**
     * Get latitude
     *
     * @return string
     */
    public function getLatitude()
    {
        return $this->getData(StoreInterface::LATITUDE);
    }
    
    /**
     * Get longitude
     *
     * @return string
     */
    public function getLongitude()
    {
        return $this->getData(StoreInterface::LONGITUDE);
    }

    /**
     * Get status
     *
     * @return bool|int
     */
    public function getStatus()
    {
        return $this->getData(StoreInterface::STATUS);
    }


    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(StoreInterface::CREATED_AT);
    }

    /**
     * Get updated at
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->getData(StoreInterface::UPDATED_AT);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return array
     */
    public function getStoreId()
    {
        return $this->getData(StoreInterface::STORE_ID);
    }

    /**
     * sanitize the url key
     *
     * @param $string
     * @return string
     */
    public function formatUrlKey($string)
    {
        return $this->filter->translitUrl($string);
    }

    /**
     * @return mixed
     */
    public function getStoreUrl()
    {
        return $this->urlModel->getStoreUrl($this);
    }

    /**
     * @return bool
     */
    public function status()
    {
        return (bool)$this->getStatus();
    }

    /**
     * @param $attribute
     * @return string
     */
    public function getAttributeText($attribute)
    {
        if (!isset($this->optionProviders[$attribute])) {
            return '';
        }
        if (!($this->optionProviders[$attribute] instanceof AbstractSource)) {
            return '';
        }
        return $this->optionProviders[$attribute]->getOptionText($this->getData($attribute));
    }
}
