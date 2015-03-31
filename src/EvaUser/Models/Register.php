<?php

namespace Eva\EvaUser\Models;

use Eva\EvaUser\Entities;
use \Phalcon\Mvc\Model\Message as Message;
use Eva\EvaEngine\Exception;

class Register extends User
{
    protected static $verificationEmailTemplate;

    public static function setVerificationEmailTemplate($template)
    {
        self::$verificationEmailTemplate = $template;
    }

    public static function getVerificationEmailTemplate()
    {
        return self::$verificationEmailTemplate;
    }

    public function register($disablePassword = false)
    {
        $this->getDI()->getEventsManager()->fire('user:beforeRegister', $this);
        $userinfo = self::findFirst("username = '$this->username'");
        if ($userinfo) {
            throw new Exception\ResourceConflictException('ERR_USER_USERNAME_ALREADY_TAKEN');
        }

        $userinfo = self::findFirst("email = '$this->email'");
        if ($userinfo) {
            throw new Exception\ResourceConflictException('ERR_USER_EMAIL_ALREADY_TAKEN');
        }

        $this->status = $this->status ?: 'inactive';
        $this->emailStatus = $this->emailStatus ?: 'inactive';
        $this->mobileStatus = $this->mobileStatus ?: 'inactive';
        $this->accountType = $this->accountType ?: 'basic';
        $this->password = $disablePassword ? null : self::passwordHash($this->password);
        $this->activationHash = sha1(uniqid(mt_rand(), true));
        $this->createdAt = time();
        $this->providerType = $this->providerType ?: 'DEFAULT';

        if ($this->save() == false) {
            throw new Exception\RuntimeException('ERR_USER_CREATE_FAILED');
        }

        $this->sendVerificationEmail($this->username);

        $this->getDI()->getEventsManager()->fire('user:afterRegister', $this);
        return $this;
    }

    public function registerByMobile($captcha)
    {
        $this->getDI()->getEventsManager()->fire('user:beforeRegister', $this);

        //手机注册要和username+email的登陆系统打通，
        //为了避免潜在的风险，username 是根据mobile生成的，mobile一样，username一定一样
//        $userinfo = self::findFirst("username = '$this->username'");
//        if ($userinfo) {
//            throw new Exception\ResourceConflictException('ERR_USER_USERNAME_ALREADY_TAKEN');
//        }

        $userinfo = self::findFirst("mobile = '$this->mobile'");
        if ($userinfo) {
            throw new Exception\ResourceConflictException('ERR_USER_MOBILE_ALREADY_TAKEN');
        }

        if (!$this->mobileCaptchaCheck($this->mobile, $captcha)) {
            throw new Exception\RuntimeException('ERR_USER_MOBILE_CAPTCHA_CHECK_FAILED');
        }


        $this->status = 'active';
        $this->emailStatus = $this->emailStatus ?: 'inactive';
        $this->mobileStatus = $this->mobileStatus ?: 'active';
        $this->accountType = $this->accountType ?: 'basic';
        $this->password = self::passwordHash($this->password);
        $this->activationHash = sha1(uniqid(mt_rand(), true));
        $this->createdAt = time();
        $this->providerType = $this->providerType ?: 'DEFAULT';

        if ($this->save() == false) {
            throw new Exception\RuntimeException('ERR_USER_CREATE_FAILED');
        }

        $this->getDI()->getEventsManager()->fire('user:afterRegister', $this);
        return $this;
    }

    public function mobileCheck($mobile)
    {

        $userinfo = self::findFirst("mobile = '$mobile'");
        if ($userinfo) {
            return false;
        }

        return true;
    }

    public function sendVerificationEmail($identify, $forceSend = false)
    {
        if (false === $forceSend && $this->getDI()->getConfig()->mailer->async) {
            $queue = $this->getDI()->getQueue();
            $result = $queue->doBackground('sendmailAsync', json_encode(array(
                'class' => __CLASS__,
                'method' => __FUNCTION__,
                'parameters' => array($identify, true)
            )));

            return true;
        }

        $userinfo = array();
        if (false === strpos($identify, '@')) {
            $userinfo = self::findFirst("username = '$identify'");
        } else {
            $userinfo = self::findFirst("email = '$identify'");
        }
        if (!$userinfo) {
            throw new Exception\ResourceNotFoundException('ERR_USER_NOT_EXIST');
        }

        if (!$userinfo->activationHash) {
            $userinfo->activationHash = sha1(uniqid(mt_rand(), true));
            $userinfo->save();
        }

        $mailer = $this->getDI()->getMailer();
        $message = $this->getDI()->getMailMessage();
        $message->setTo(array(
            $userinfo->email => $userinfo->username
        ));
        $template = self::getVerificationEmailTemplate();
        $template = $template ?: $this->getDI()->getConfig()->user->activeMailTemplate;
        $message->setTemplate($template);
        $message->assign(array(
            'user' => $userinfo->toArray(),
            'url' => $message->toSystemUrl('/session/verify/' . urlencode($userinfo->username) . '/' . $userinfo->activationHash)
        ));
        $mailer->send($message->getMessage());
        return true;
    }

    /**
     * checks the email/verification code combination and set the user's activation status to active in the database
     * @param int $username
     * @param string $activationCode
     * @return bool success status
     */
    public function verifyNewUser($username, $activationCode)
    {
        $userinfo = self::findFirst("username = '$username'");
        if (!$userinfo) {
            throw new Exception\ResourceNotFoundException('ERR_USER_NOT_EXIST');
        }

        if ($userinfo->status == 'active' && $userinfo->emailStatus == 'active') {
            throw new Exception\OperationNotPermitedException('ERR_USER_ALREADY_ACTIVED');
        }

        if ($userinfo->status == 'deleted') {
            throw new Exception\OperationNotPermitedException('ERR_USER_BE_BANNED');
        }

        if ($userinfo->activationHash != $activationCode) {
            throw new Exception\VerifyFailedException('ERR_USER_ACTIVATE_CODE_NOT_MATCH');
        }

        $userinfo->status = 'active';
        $userinfo->activedAt = time();
        $userinfo->emailStatus = 'active';
        $userinfo->emailConfirmedAt = time();
        if (!$userinfo->save()) {
            throw new Exception\RuntimeException('ERR_USER_ACTIVE_FAILED');
        }

        return true;
    }

    public function mobileCaptcha($mobile)
    {
        $cache = $this->getDI()->get('modelsCache');

        $cacheKey = 'sms_captcha_' . $mobile;
//        dd($cacheKey);

        $now = time();
        if ($cache->exists($cacheKey)) {
            $data = $cache->get($cacheKey);
            //60s内不重复发送
            if (($now - $data['timestamp']) < 60) {
                return;
            }
        }

        /** @var \Eva\EvaSms\Sender $sender */
        $sender = $this->getDI()->getSmsSender();
        $captcha = mt_rand(100000, 999999);
        $templateId = $this->getDI()->getConfig()->smsSender->templates->verifyCode;
        $result = $sender->sendTemplateMessage($mobile, $templateId, ['number' => $captcha]);

        $data['timestamp'] = $now;
        $data['captcha'] = $captcha;

        //一小时内有效
        $cacheTime = 1 * 60 * 60;
        $cache->save($cacheKey, $data, $cacheTime);

        return $result;
    }


}
