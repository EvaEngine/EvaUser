<?php

namespace Eva\EvaUser\Entities;

class UserAuththread extends \Eva\EvaEngine\Mvc\Model
{
    protected $tableName = 'user_realname_auth_thread';

    public $id;

    public $requestTime;

    public $quantity;

    public $status;

}
