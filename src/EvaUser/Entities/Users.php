<?php

namespace Eva\EvaUser\Entities;

use Phalcon\Mvc\Model\Validator\Email as Email;

class Users extends \Eva\EvaEngine\Mvc\Model
{
    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $username;

    /**
     *
     * @var string
     */
    public $email;

    /**
     *
     * @var string
     */
    public $mobile;

    /**
     *
     * @var string
     */
    public $status = 'inactive';

    /**
     *
     * @var string
     */
    public $accountType = 'basic';

    /**
     *
     * @var string
     */
    public $screenName;

    /**
     *
     * @var string
     */
    public $firstName;

    /**
     *
     * @var string
     */
    public $lastName;

    /**
     *
     * @var string
     */
    public $password;

    /**
     *
     * @var string
     */
    public $oldPassword;

    /**
     *
     * @var string
     */
    public $gender;

    /**
     *
     * @var integer
     */
    public $avatarId;

    /**
     *
     * @var string
     */
    public $avatar;

    /**
     *
     * @var string
     */
    public $timezone;

    /**
     *
     * @var string
     */
    public $language;

    /**
     *
     * @var string
     */
    public $emailStatus = 'inactive';

    /**
     *
     * @var integer
     */
    public $emailConfirmedAt;

    /**
     *
     * @var integer
     */
    public $createdAt;

    /**
     *
     * @var integer
     */
    public $loginAt;

    /**
     *
     * @var string
     */
    public $failedLogins;

    /**
     *
     * @var integer
     */
    public $loginFailedAt;

    /**
     *
     * @var string
     */
    public $activationHash;

    /**
     *
     * @var integer
     */
    public $activedAt;

    /**
     *
     * @var string
     */
    public $passwordResetHash;

    /**
     *
     * @var integer
     */
    public $passwordResetAt;

    /**
     *
     * @var string
     */
    public $providerType = 'DEFAULT';

//    /**
//     *
//     * @var string 扩展字段
//     */
//    public $extension = '00000000';

    protected $tableName = 'user_users';

    public static $cache = null;

    private $cachePrefix = 'eva_blog_user_';

    private $cacheTime = 300;

    public function getCache()
    {
        if(self::$cache === null){
            /** @var \Phalcon\Cache\Backend\Libmemcached $cache */
            $cache =  $this->getDI()->get('modelsCache');
            self::$cache = $cache;
        }
        return self::$cache;
    }

    public function refreshCache()
    {
        $cacheKey = $this->getCacheKey();
        if($this->getCache()->exists($cacheKey)){
            $this->getCache()->delete($cacheKey);
        }
//        var_dump($error);
//        $results = $this->stars;
//        $error = $this->getCache()->save($cacheKey,$results,$this->cacheTime);
//        $error = $this->getCache()->exists($cacheKey);


    }

//    public function afterSave()
//    {
//        $this->refreshCache();
//    }

    public function getStars()
    {
        $cacheKey = $this->getCacheKey();

        $results = $this->getCache()->get($cacheKey);
        if($results){
            return $results;
        }

        $results = $this->stars;
        $this->getCache()->save($cacheKey,$results,$this->cacheTime);
        return $results;

    }

    public function getCacheKey()
    {
        return $this->cachePrefix.$this->id;

    }

    /**
     * Validations and business logic
     */
    public function validation()
    {
        $this->validate(
            new Email(
                array(
                    "field" => "email",
                    "required" => true,
                )
            )
        );
        if ($this->validationHasFailed() == true) {
            return false;
        }
    }


    public function isSuperUser()
    {
        return false;
    }

    public function isActived()
    {
        return $this->status == 'active';
    }

    public function isBlocked()
    {
        return $this->status == 'deleted';
    }

    public function getRoles()
    {
        if (!$this->id) {
            return array('GUEST');
        }

        if ($this->isActived()) {
            return array('USER');
        } else {
            return array('PENDING_USER');
        }
    }

    public function getAvatar()
    {
        if ($this->avatar) {
            return $this->avatar;
        }
        return 'http://www.gravatar.com/avatar/' . md5(strtolower(trim($this->email)));
    }

    public function getName()
    {
        return $this->screenName ?: $this->username;
    }

    public function initialize()
    {
        $this->hasOne(
            'id',
            'Eva\EvaUser\Entities\Profiles',
            'userId',
            array(
                'alias' => 'profile'
            )
        );

        $this->hasMany(
            'id',
            'Eva\EvaBlog\Entities\Stars',
            'userId',
            array(
                'alias' => 'stars'
            )
        );

        parent::initialize();
    }
}
