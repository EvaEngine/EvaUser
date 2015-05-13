<?php
/**
 * Created by PhpStorm.
 * User: wscn
 * Date: 15/4/23
 * Time: 下午4:32
 */

namespace Eva\EvaUser\Entities;


use Eva\EvaEngine\Mvc\Model;

class EvaUserEntityBase extends Model
{
    protected $useMasterSlave = false;

    public function initialize()
    {
        $this->setReadConnectionService('userDbSlave');
        $this->setWriteConnectionService('userDbMaster');
        parent::initialize();
    }
}