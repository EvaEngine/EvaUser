<?php

namespace Eva\EvaUser\Entities;
use \Eva\EvaEngine\Mvc\Model;
use Eva\EvaEngine\Exception;
/**
 * @SWG\Model(id="RealnameAuthForm")
 */
class RealnameAuth extends Model
{
    public $userId;
    /**
     * 姓名
     * @var string
     * @SWG\Property
     */
    public $realName;
    /**
     * 身份证号码
     * @var string
     * @SWG\Property
     */
    public $cardNum;
    public $status = 0;
    public $createTime;
    protected $tableName = 'user_realname_auth';

}
