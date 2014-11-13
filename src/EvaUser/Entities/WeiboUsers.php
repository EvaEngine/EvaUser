<?php

namespace Eva\EvaUser\Entities;

class WeiboUsers extends \Eva\EvaEngine\Mvc\Model implements CommentUser
{
    protected $tableName = 'weibo_users';
    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $screenName;

    /**
     *
     * @var string
     */
    public $avatar;

    const USER_TYPE='weibo';

    public function getId()
    {
        return $this->avatar;
    }

    public function getName()
    {
        return $this->screenName ?: $this->name;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }

    public function getUserType()
    {
        return self::USER_TYPE;
    }

}
