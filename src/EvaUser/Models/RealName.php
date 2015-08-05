<?php
/**
 * Created by PhpStorm.
 * User: yudoudou
 * Date: 15/8/5
 * Time: 上午10:20
 */

namespace Eva\EvaUser\Models;

use Eva\EvaEngine\Mvc\Model\Validator\Uniqueness;
use Eva\EvaUser\Entities\RealnameAuth;
use Phalcon\Mvc\Model\Validator\PresenceOf;
use Phalcon\Mvc\Model\Validator\Regex;
use Eva\EvaUser\Models\Validator\ValidRealName;

class RealName extends RealnameAuth
{
    public function getValidators()
    {
        return [
            new PresenceOf([
                'field' => 'realName',
                'message' => '真实姓名不能为空'
            ]),
            // 下面有更强的验证，这个先去掉
            /*
            new Regex([
                'field' => 'realName',
                'message' => '真实姓名的格式不正确',
                'pattern' => "/^[一-龥]+$/"
            ]),
            */
            new PresenceOf([
                'field' => 'cardNum',
                'message' => '身份证号不能为空'
            ]),
            new Regex([
                'field' => 'cardNum',
                'message' => '身份证号的格式不正确',
                'pattern' => '/(^\d{15}$)|(^\d{17}([0-9]|X)$)/'
            ]),
            new Uniqueness([
                'field' => 'cardNum',
                'message' => '此身份证号已被使用'
            ]),
            new ValidRealName([
                'message' => '请输入有效的名字和身份证号码'
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
