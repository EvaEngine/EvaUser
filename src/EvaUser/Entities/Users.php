<?php

namespace Eva\EvaUser\Entities;

use Eva\EvaComment\Models\CommentManager;
use Phalcon\Mvc\Model\Validator\Email as Email;

class Users extends EvaUserEntityBase implements CommentUser
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
     * @var int
     */
    public $usernameCustomized = 1;
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
     * @var string
     */
    public $mobileStatus = 'inactive';

    /**
     *
     * @var integer
     */
    public $mobileConfirmedAt;

    /**
     *
     * @var integer
     */
    public $createdAt;

    /**
     *
     * @var integer
     */
    public $updatedAt;

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

    /**
     *
     * @var string
     */
    public $source;

    /**
     *
     * @var integer
     */
    public $spamReason;

    const SPAM_REASON_DEFAULT = 0;
    const SPAM_REASON_COMMENTS_TOO_MUCH = 1;
    const SPAM_REASON_FILTER_TOO_MUCH = 2;
    const SPAM_REASON_REPORT_TOO_MUCH = 3;

    public $spamReasonMap = array(
        self::SPAM_REASON_DEFAULT => '',
        self::SPAM_REASON_COMMENTS_TOO_MUCH => "评论过多",
        self::SPAM_REASON_FILTER_TOO_MUCH => "多次被过滤",
        self::SPAM_REASON_REPORT_TOO_MUCH => "多次被举报"
    );

//    /**
//     *
//     * @var string 扩展字段
//     */
//    public $extension = '00000000';

    protected $tableName = 'user_users';

    const USER_TYPE = 'wscn';

    const STATE_ACTIVE = 'active';


    /**
     * Validations and business logic
     */
    public function validation()
    {
        if (empty($this->mobile)) {
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
        if (!$this->isActived()) {
            return array('PENDING_USER');
        }
        
        $roles = array('USER');
        foreach ($this->roles as $role) {
            $roles[] = $role->roleKey;
        }
        
        return $roles;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAvatar()
    {
        if (!$this->avatar) {
            return 'http://avatar.cdn.wallstcn.com/default';
        }
//        return 'http://www.gravatar.com/avatar/' . md5(strtolower(trim($this->email)));
        return $this->avatar;
    }

    public function getName()
    {
        return $this->screenName ?: $this->username;
    }

    public function getUserType()
    {
        return self::USER_TYPE;
    }

    public function getLastComment()
    {
        $commentManager = new CommentManager();
        $lastComment = $commentManager->findLastCommentByUserId($this->id);
        return $lastComment;
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
        $this->hasOne('id', 'Eva\EvaUser\Entities\RealnameAuth', 'userId',
            array(
                'alias' => 'auth'
            )
        );

        parent::initialize();
    }
}
