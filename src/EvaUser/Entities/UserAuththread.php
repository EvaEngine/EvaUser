<?php

namespace Eva\EvaUser\Entities;

class UserAuththread extends EvaUserEntityBase
{
    protected $tableName = 'user_realname_auth_log';

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $userId;

    /**
     *
     * @var string
     */
    public $realName;


    /**
     *
     * @var string
     */
    public $cardNum;

    /**
     *
     * @var integer
     */
    public $requestAt;

    /**
     *
     * @var integer
     */
    public $quantity = 1;

    /**
     *
     * @var string
     */
    public $status;

    /**
     *
     * @var string
     */
    public $compStatus;

    /**
     *
     * @var string
     */
    public $message;

    /**
     *
     * @var string
     */
    public $compMessage;

}
