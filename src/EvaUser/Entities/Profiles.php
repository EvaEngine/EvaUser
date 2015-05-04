<?php

namespace Eva\EvaUser\Entities;

class Profiles extends \Eva\EvaEngine\Mvc\Model
{
    /**
     *
     * @var integer
     */
    public $userId;

    /**
     *
     * @var string
     */
    public $site;

    /**
     *
     * @var string
     */
    public $photoDir;

    /**
     *
     * @var string
     */
    public $photoName;

    /**
     *
     * @var string
     */
    public $fullName;

    /**
     *
     * @var string
     */
    public $birthday;

    /**
     *
     * @var string
     */
    public $relationshipStatus;

    /**
     *
     * @var string
     */
    public $height;

    /**
     *
     * @var string
     */
    public $weight;

    /**
     *
     * @var string
     */
    public $country;

    /**
     *
     * @var string
     */
    public $address;

    /**
     *
     * @var string
     */
    public $addressMore;

    /**
     *
     * @var string
     */
    public $city;

    /**
     *
     * @var string
     */
    public $province;

    /**
     *
     * @var string
     */
    public $state;

    /**
     *
     * @var string
     */
    public $zipcode;

    /**
     *
     * @var string
     */
    public $degree;

    /**
     *
     * @var string
     */
    public $industry;

    /**
     *
     * @var string
     */
    public $interest;

    /**
     *
     * @var string
     */
    public $phoneBusiness;

    /**
     *
     * @var string
     */
    public $phoneMobile;

    /**
     *
     * @var string
     */
    public $phoneHome;

    /**
     *
     * @var string
     */
    public $fax;

    /**
     *
     * @var string
     */
    public $signature;

    /**
     *
     * @var string
     */
    public $longitude;

    /**
     *
     * @var string
     */
    public $latitude;

    /**
     *
     * @var string
     */
    public $location;

    /**
     *
     * @var string
     */
    public $bio;

    /**
     *
     * @var string
     */
    public $localIm;

    /**
     *
     * @var string
     */
    public $internalIm;

    /**
     *
     * @var string
     */
    public $otherIm;

    /**
     *
     * @var string
     */
    public $company;

    /**
     *
     * @var int
     */
    public $faceNum;

    /**
     *
     * @var integer
     */
    public $updatedAt;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'userId' => 'userId',
            'site' => 'site',
            'photoDir' => 'photoDir',
            'photoName' => 'photoName',
            'fullName' => 'fullName',
            'birthday' => 'birthday',
            'relationshipStatus' => 'relationshipStatus',
            'height' => 'height',
            'weight' => 'weight',
            'country' => 'country',
            'address' => 'address',
            'addressMore' => 'addressMore',
            'city' => 'city',
            'province' => 'province',
            'state' => 'state',
            'zipcode' => 'zipcode',
            'degree' => 'degree',
            'industry' => 'industry',
            'interest' => 'interest',
            'phoneBusiness' => 'phoneBusiness',
            'phoneMobile' => 'phoneMobile',
            'phoneHome' => 'phoneHome',
            'fax' => 'fax',
            'signature' => 'signature',
            'longitude' => 'longitude',
            'latitude' => 'latitude',
            'location' => 'location',
            'bio' => 'bio',
            'localIm' => 'localIm',
            'internalIm' => 'internalIm',
            'otherIm' => 'otherIm',
            'company' => 'company',
            'faceNum' => 'faceNum',
            'updatedAt' => 'updatedAt'
        );
    }

    protected $tableName = 'user_profiles';
}
