<?php
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
namespace Kinspeed\Stores\Api\Data;

/**
 * @api
 */
interface StoreInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ENTITY_ID         = 'entity_id';
    const NAME                = 'name';
    const ADDRESS             = 'address';
    const CITY                = 'city';
    const POSTCODE            = 'postcode';
    const REGION              = 'region';
    const EMAIL               = 'email';
    const PHONE               = 'phone';
    const LATITUDE            = 'latitude';
    const LONGITUDE           = 'longitude';
    const LINK                = 'link';
    const STATUS              = 'status';
    const TYPE                = 'type';
    const COUNTRY             = 'country';
    const IMAGE               = 'image';
    const CREATED_AT          = 'created_at';
    const UPDATED_AT          = 'updated_at';
    const STORE_ID            = 'store_id';
    const SCHEDULE            = 'schedule';
    const INTRO               = 'intro';
    const DESCRIPTION         = 'description';
    const DISTANCE            = 'distance';
    const STATION             = 'station';
    const DETAILS_IMAGE       = 'details_image';
    const EXTERNAL_LINK       = 'external_link';


    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get schedule
     *
     * @return string
     */
    public function getSchedule();


    /**
     * Get intro
     *
     * @return string
     */
    public function getIntro();

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Get external link
     *
     * @return string
     */
    public function getExternalLink();

    /**
     * Get distance
     *
     * @return string
     */
    public function getDistance();

    /**
     * Get station
     *
     * @return string
     */
    public function getStation();

    /**
     * Get store details image
     *
     * @return string
     */
    public function getDetailsImage();


    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Get store url
     *
     * @return string
     */
    public function getLink();
    
    /**
     * Get address
     *
     * @return string
     */
    public function getAddress();
    
    /**
     * Get city
     *
     * @return string
     */
    public function getCity();
    
    /**
     * Get postcode
     *
     * @return string
     */
    public function getPostcode();
    
    /**
     * Get region
     *
     * @return string
     */
    public function getRegion();
    
    /**
     * Get email
     *
     * @return string
     */
    public function getEmail();
    
    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone();
    
    /**
     * Get image
     *
     * @return string
     */
    public function getImage();
    
    /**
     * Get latitude
     *
     * @return string
     */
    public function getLatitude();
    
    /**
     * Get longitude
     *
     * @return string
     */
    public function getLongitude();

    /**
     * Get is active
     *
     * @return bool|int
     */
    public function getStatus();

    /**
     * Get type
     *
     * @return int
     */
    public function getType();

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry();

    /**
     * set id
     *
     * @param $id
     * @return StoreInterface
     */
    public function setId($id);

    /**
     * set name
     *
     * @param $name
     * @return StoreInterface
     */
    public function setName($name);

    /**
     * set link
     *
     * @param $link
     * @return StoreInterface
     */
    public function setLink($link);
    
    /**
     * set image
     *
     * @param $image
     * @return AuthorInterface
     */
    public function setImage($image);
    
    /**
     * set address
     *
     * @param $address
     * @return StoreInterface
     */
    public function setAddress($address);

    /**
     * set city
     *
     * @param $city
     * @return StoreInterface
     */
    public function setCity($city);
    
    /**
     * set postcode
     *
     * @param $postcode
     * @return StoreInterface
     */
    public function setPostcode($postcode);


    /**
     * set schedule
     *
     * @param $schedule
     * @return StoreInterface
     */
    public function setSchedule($schedule);

    /**
     * set description
     *
     * @param $description
     * @return StoreInterface
     */
    public function setDescription($description);

    /**
     * set distance
     *
     * @param $distance
     * @return StoreInterface
     */
    public function setDistance($distance);

    /**
     * set station
     *
     * @param $station
     * @return StoreInterface
     */
    public function setStation($station);

    /**
     * set external link
     *
     * @param $external_link
     * @return StoreInterface
     */
    public function setExternalLink($external_link);

    /**
     * set intro
     *
     * @param $intro
     * @return StoreInterface
     */
    public function setIntro($intro);

    /**
     * set store details image
     *
     * @param $details_image
     * @return StoreInterface
     */
    public function setDetailsImage($details_image);

    /**
     * set region
     *
     * @param $region
     * @return StoreInterface
     */
    public function setRegion($region);

    /**
     * set email
     *
     * @param $email
     * @return StoreInterface
     */
    public function setEmail($email);
    
    /**
     * set phone
     *
     * @param $phone
     * @return StoreInterface
     */
    public function setPhone($phone);

    /**
     * set latitude
     *
     * @param $latitude
     * @return StoreInterface
     */
    public function setLatitude($latitude);
    
    /**
     * set longitude
     *
     * @param $longitude
     * @return StoreInterface
     */
    public function setLongitude($longitude);

    /**
     * Set status
     *
     * @param $status
     * @return StoreInterface
     */
    public function setStatus($status);

    /**
     * set type
     *
     * @param $type
     * @return StoreInterface
     */
    public function setType($type);

    /**
     * Set country
     *
     * @param $country
     * @return StoreInterface
     */
    public function setCountry($country);

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * set created at
     *
     * @param $createdAt
     * @return StoreInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get updated at
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * set updated at
     *
     * @param $updatedAt
     * @return StoreInterface
     */
    public function setUpdatedAt($updatedAt);

    /**
     * @param $storeId
     * @return StoreInterface
     */
    public function setStoreId($storeId);

    /**
     * @return int[]
     */
    public function getStoreId();

}
