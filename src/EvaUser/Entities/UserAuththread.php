<?php

namespace Eva\EvaUser\Entities;

class UserAuththread extends EvaUserEntityBase
{
    protected $tableName = 'user_realname_auth_thread';

    public $id;

    public $requestTime;

    public $quantity;

    public $status;

}
