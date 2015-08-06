<?php
/**
 * Created by PhpStorm.
 * User: yudoudou
 * Date: 15/8/6
 * Time: 下午1:09
 */

namespace Eva\EvaUser\Models;

use Eva\EvaUser\Entities\Users;
use Eva\EvaEngine\Mvc\Model\Validator\Uniqueness;
use Eva\EvaUser\Models\Validator\MobileCaptcha;
use Phalcon\Mvc\Model\Validator\PresenceOf;

/**
 * @property RealName $realName 实名信息record
 * Class PayUser
 */
class PayUser extends Users
{
    /**
     * 非数据库字段，当设置手机号的时候用到
     * @var string
     */
    public $mobileCaptcha;

    public function initialize()
    {
        $this->hasOne(
            'id',
            'Eva\EvaUser\Models\RealName',
            'userId',
            array(
                'alias' => 'realName'
            )
        );
        parent::initialize();
    }

    public function getValidators()
    {
        return [
            new PresenceOf([
                'field' => 'mobile',
                'message' => '手机号不能为空'
            ]),
            new PresenceOf([
                'field' => 'mobileCaptcha',
                'message' => '验证码不能为空'
            ]),
            new MobileCaptcha([
                'captchaField' => 'mobileCaptcha',
            ]),
            new Uniqueness([
                'field' => 'mobile',
                'message' => '此手机号已被使用'
            ]),
        ];
    }

    public function validation()
    {
        foreach ($this->getValidators() as $validator) {
            $this->validate($validator);
            // always cancelOnFail
            if ($this->validationHasFailed()) {
                return false;
            }
        }
        return true;
    }
}