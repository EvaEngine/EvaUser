<?php

namespace Eva\EvaUser\Entities;
use Eva\EvaEngine\Exception;
/**
 * @SWG\Model(id="RealnameAuthForm")
 */
class RealnameAuth extends EvaUserEntityBase
{
    //身份证号码不存在
    const STATUS_UNDEFINED = 1;

    //身份证号号码与姓名不一致
    const STATUS_UNMATCH = 2;

    //验证成功
    const STATUS_SUCCESS = 3;

    //已提交，验证中
    const STATUS_VERIFYING = 4;

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
    public $status = 3;
    public $createTime;
    protected $tableName = 'user_realname_auth';

}
