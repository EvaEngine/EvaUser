<?php
namespace Eva\EvaUser\Forms;

use Eva\EvaEngine\Form;

/**
 * Class ProfileForm
 * @package Eva\EvaUser\Forms
 * @SWG\Model(id="ProfileForm")
 */
class ProfileForm extends Form
{
    /**
     *
     * @var integer
     */
    public $userId;

    /**
     * 个人主页
     * @var string
     * @SWG\Property
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
     * 生日
     * @var string
     * @SWG\Property
     */
    public $birthday;

    /**
     *
     * @var string
     */
    public $relationshipStatus;

    /**
     * 身高
     * @var string
     * @SWG\Property
     */
    public $height;

    /**
     * 体重
     * @var string
     * @SWG\Property
     */
    public $weight;

    /**
     * 国家
     *
     * @var string
     * @SWG\Property
     */
    public $country;

    /**
     * 地址
     *
     * @var string
     * @SWG\Property
     */
    public $address;

    /**
     * 地址第二行
     *
     * @var string
     * @SWG\Property
     */
    public $addressMore;

    /**
     * 城市
     * @var string
     * @SWG\Property
     */
    public $city;

    /**
     * 省份
     *
     * @var string
     * @SWG\Property
     */
    public $province;

    /**
     * 州
     * @var string
     * @SWG\Property
     */
    public $state;

    /**
     * 邮编
     * @var string
     * @SWG\Property
     */
    public $zipcode;

    /**
     * 学历
     * @var string
     * @SWG\Property
     */
    public $degree;

    /**
     * 行业
     * @var string
     * @SWG\Property
     */
    public $industry;

    /**
     * 兴趣爱好
     * @var string
     * @SWG\Property
     */
    public $interest;

    /**
     * 办公电话
     * @var string
     * @SWG\Property
     */
    public $phoneBusiness;

    /**
     * 手机号码
     * @var string
     * @SWG\Property
     */
    public $phoneMobile;

    /**
     * 家庭电话
     * @var string
     * @SWG\Property
     */
    public $phoneHome;

    /**
     * 传真
     * @Type(Textarea)
     * @var string
     * @SWG\Property
     */
    public $fax;

    /**
     * @var string
     */
    public $signature;

    /**
     * 经度
     * @var string
     * @SWG\Property
     */
    public $longitude;

    /**
     * 纬度
     * @var string
     * @SWG\Property
     */
    public $latitude;

    /**
     *
     * @var string
     */
    public $location;

    /**
     * 自我介绍
     * @var string
     * @SWG\Property
     * @Type(Textarea)
     */
    public $bio;

    /**
     * QQ 号
     * @var string
     * @SWG\Property
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
