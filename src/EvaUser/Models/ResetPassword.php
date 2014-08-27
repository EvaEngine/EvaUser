<?php

namespace Eva\EvaUser\Models;

use Eva\EvaUser\Entities;
use \Phalcon\Mvc\Model\Message as Message;
use Eva\EvaEngine\Exception;

class ResetPassword extends User
{
    protected $resetPasswordHashExpired = 3600;

    public function requestResetPassword()
    {
        $userinfo = array();
        if ($this->username) {
            $userinfo = self::findFirst("username = '$this->username'");
        } elseif ($this->email) {
            $userinfo = self::findFirst("email = '$this->email'");
        }

        if (!$userinfo) {
            throw new Exception\ResourceNotFoundException('ERR_USER_NOT_EXIST');
        }

        //status tranfer only allow inactive => active
        if ($userinfo->status != 'active') {
            throw new Exception\OperationNotPermitedException('ERR_USER_NOT_ACTIVED');
        }

        if ($userinfo->emailStatus != 'active') {
            throw new Exception\OperationNotPermitedException('ERR_USER_EMAIL_NOT_ACTIVED');
        }

        // generate random hash for email password reset verification (40 char string)
        $userinfo->passwordResetHash = sha1(uniqid(mt_rand(), true));
        $userinfo->passwordResetAt = time();
        if (!$userinfo->save()) {
            throw new Exception\RuntimeException('ERR_USER_REQUEST_RESET_PASSWORD_FAILED');
        }

        $this->sendPasswordResetMail($userinfo->email);

        return true;
    }

    public function sendPasswordResetMail($email, $forceSend = false)
    {
        if (false === $forceSend && $this->getDI()->getConfig()->mailer->async) {
            $queue = $this->getDI()->get('queue');
            $result = $queue->doBackground('sendmailAsync', json_encode(array(
                'class' => __CLASS__,
                'method' => __FUNCTION__,
                'parameters' => array($email, true)
            )));

            return true;
        }

        $userinfo = self::findFirst("email= '$email'");
        if (!$userinfo) {
            throw new Exception\ResourceNotFoundException('ERR_USER_NOT_EXIST');
        }

        $mailer = $this->getDI()->get('mailer');
        $message = $this->getDI()->get('mailMessage');
        $message->setTo(array(
            $userinfo->email => $userinfo->username
        ));
        $message->setTemplate($this->getDI()->getConfig()->user->resetMailTemplate);
        $message->assign(array(
            'user' => $userinfo->toArray(),
            'url' => $message->toSystemUrl('/session/reset/' . urlencode($userinfo->username) . '/' . $userinfo->passwordResetHash)
        ));

        return $mailer->send($message->getMessage());
    }

    /**
    * Verifies the password reset request via the verification hash token (that's only valid for one hour)
    * @param string $username Username
    * @param string $verificationCode Hash token
    * @return bool Success status
    */
    public function verifyPasswordReset($username, $verificationCode)
    {
        $userinfo = self::findFirst("username = '$username'");
        if (!$userinfo) {
            throw new Exception\ResourceNotFoundException('ERR_USER_NOT_EXIST');
        }

        if ($userinfo->status != 'active') {
            throw new Exception\OperationNotPermitedException('ERR_USER_NOT_ACTIVED');
        }

        if ($userinfo->passwordResetHash != $verificationCode) {
            throw new Exception\VerifyFailedException('ERR_USER_RESET_CODE_NOT_MATCH');
        }

        if ($userinfo->passwordResetAt < time() - $this->resetPasswordHashExpired) {
            throw new Exception\ResourceExpiredException('ERR_USER_RESET_CODE_EXPIRED');
        }

        return true;
    }

    public function resetPassword()
    {
        if (!$this->password) {
            throw new Exception\InvalidArgumentException('ERR_USER_NO_NEW_PASSWORD_INPUT');
        }

        $userinfo = self::findFirst("username = '$this->username'");
        if (!$userinfo) {
            throw new Exception\ResourceNotFoundException('ERR_USER_NOT_EXIST');
        }

        $userinfo->password = self::passwordHash($this->password);
        //make last hash expire
        $userinfo->passwordResetHash = sha1(uniqid(mt_rand(), true));
        if (!$userinfo->save()) {
            throw new Exception\RuntimeException('ERR_USER_RESET_PASSWORD_FAILED');
        }

        return true;
    }
}
