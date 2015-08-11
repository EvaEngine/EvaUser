<?php
/**
 * Created by PhpStorm.
 * User: yudoudou
 * Date: 15/8/6
 * Time: 下午12:53
 */

namespace Eva\EvaUser\Models\Validator;

use Eva\EvaSecurity\Verification\Verification;
use Phalcon\Mvc\Model\Validator;

/**
 * 验证手机的验证码是否正确
 * Class MobileCaptcha
 */
class MobileCaptcha extends Validator
{
    public function validate($model)
    {
        $mobileField = $this->getOption('mobileField') ?: 'mobile';
        $captchaField = $this->getOption('captchaField') ?: 'captcha';

        if (!Verification::factory($model->$mobileField, 'sms', 'new_mobile')->verify($model->$captchaField)) {
            $messageStr = $this->getOption('message') ?: '手机号验证码不正确';
            $this->appendMessage($messageStr, $captchaField, 'MobileCaptcha');
            return false;
        }
        return true;
    }
}