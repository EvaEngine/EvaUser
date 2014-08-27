<?php
namespace Eva\EvaUser\Forms;

use Eva\EvaEngine\Form;

class ProfileForm extends Form
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
     * @Type(Textarea)
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
     * @var integer
     */
    public $updatedAt;

    protected $defaultModelClass = 'Eva\EvaUser\Models\Profile';

    public function initialize($entity = null, $options = null)
    {
    }
}
