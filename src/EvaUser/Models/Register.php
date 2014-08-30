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
        $this->accountType = $this->accountType ?: 'basic';
        $this->password = $disablePassword ? null : self::passwordHash($this->password);
        $this->activationHash = sha1(uniqid(mt_rand(), true));
        $this->createdAt = time();
        $this->providerType = $this->providerType ?: 'DEFAULT';

        if ($this->save() == false) {
            throw new Exception\RuntimeException('ERR_USER_CREATE_FAILED');
        }

        $userinfo = self::findFirst("username = '$this->username'");
        if (!$userinfo) {
            throw new Exception\RuntimeException('ERR_USER_CREATE_FAILED');
        }
        $this->sendVerificationEmail($userinfo->username);

        $this->getDI()->getEventsManager()->fire('user:afterRegister', $this);
        return $userinfo;
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
}
