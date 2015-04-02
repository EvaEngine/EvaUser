<?php

namespace Eva\EvaUser\Models;

use Eva\EvaUser\Entities;
use Eva\EvaEngine\Exception;
use Eva\EvaFileSystem\Models\Upload as UploadModel;
use Eva\EvaUser\Forms\MobileBindingForm;

class User extends Entities\Users
{
    public static $simpleDump = array(
        'id',
        'username',
        'email',
        'status',
        'emailStatus',
        'screenName',
        'avatar' => 'getAvatar',
        'roles' => 'getRoles',
    );
    // 登录后写入到 session 的数据
    public static $dumpForAuth = array(
        'id',
        'username',
        'status',
        'email',
        'emailStatus',
        'mobile',
        'mobileStatus',
        'screenName',
        'avatar' => 'getAvatar',
    );

    public function beforeSave()
    {
//        if ($this->getDI()->getRequest()->hasFiles()) {
//            $upload = new UploadModel();
//            $files = $this->getDI()->getRequest()->getUploadedFiles();
//            if (!$files) {
//                return;
//            }
//            $file = $files[0];
//            $file = $upload->upload($file);
//            if ($file) {
//                $this->avatarId = $file->id;
//                $this->avatar = $file->getFullUrl();
//            }
//        }
    }

    public function changePassword($oldPassword, $newPassword)
    {
        $me = Login::getCurrentUser();
        $userId = $me['id'];
        if (!$userId) {
            throw new Exception\UnauthorizedException('ERR_USER_NOT_LOGIN');
        }

        $user = self::findFirst("id = $userId");
        if (!$user) {
            throw new Exception\ResourceNotFoundException('ERR_USER_NOT_EXIST');
        }

        $user->password = self::passwordHash($newPassword);
        if (!$user->save()) {
            throw new Exception\RuntimeException('ERR_USER_CHANGE_PASSWORD_FAILED');
        }
        return $user;
    }

    /**
     * 加密密码
     *
     * @param $password
     * @return bool|false|string
     */
    public static function passwordHash($password)
    {
        return password_hash($password, PASSWORD_DEFAULT, array('cost' => 10));
    }

    /**
     * 验证密码
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public static function passwordVerify($password, $hash)
    {
        return password_verify($password, $hash);
    }

    public function changeAvatar()
    {
    }

    public function changeProfile($data)
    {
        $user = Login::getCurrentUser();
        if ($user['id'] != $this->id) {
            throw new Exception\UnauthorizedException('ERR_USER_NO_ALLOW_TO_ACCESS_OTHER_USER');
        }

        $profileData = empty($data['profile']) ? array() : $data['profile'];
        $profile = new Profile();
        $profile->assign($profileData);
        $this->profile = $profile;

        $this->assign($data);
        if (!$this->save()) {
            throw new Exception\RuntimeException('Create user failed');
        }

        return $this;
    }

    public function requestChangeEmail($newEmail, $forceSend = false)
    {
        $me = Login::getCurrentUser();
        $userId = $me['id'];
        if (!$userId) {
            throw new Exception\UnauthorizedException('ERR_USER_NOT_LOGIN');
        }

        $user = self::findFirst("id = $userId");
        if (!$user) {
            throw new Exception\ResourceNotFoundException('ERR_USER_NOT_EXIST');
        }
        return $this->sendChangeEmailVerificationEmail($user->username, $newEmail);
    }

    public function sendChangeEmailVerificationEmail($username, $newEmail, $forceSend = false)
    {
        if (false === $forceSend && $this->getDI()->getConfig()->mailer->async) {
            $queue = $this->getDI()->getQueue();
            $result = $queue->doBackground(
                'sendmailAsync',
                json_encode(
                    array(
                        'class' => __CLASS__,
                        'method' => __FUNCTION__,
                        'parameters' => array($username, $newEmail, true)
                    )
                )
            );
            return true;
        }

        $user = self::findFirst("username = '$username'");
        if (!$user) {
            throw new Exception\ResourceNotFoundException('ERR_USER_NOT_EXIST');
        }

        $mailer = $this->getDI()->getMailer();
        $message = $this->getDI()->getMailMessage();
        $message->setTo(
            array(
                $newEmail => $user->username
            )
        );

        //Change email hash will expired when password / email changed
        $verifyCode = md5($user->id . $user->password . $user->email . $newEmail);
        $message->setTemplate($this->getDI()->getConfig()->user->changeEmailTemplate);
        $message->assign(
            array(
                'user' => $user->toArray(),
                'url' => $message->toSystemUrl(
                    '/session/changemail/' . urlencode($user->username) . '/' . urlencode(
                        $newEmail
                    ) . '/' . $verifyCode
                )
            )
        );

        $mailer->send($message->getMessage());
    }

    public function changeEmail($username, $newEmail, $verifyCode)
    {
        $user = self::findFirst("username = '$username'");
        if (!$user) {
            throw new Exception\ResourceNotFoundException('ERR_USER_NOT_EXIST');
        }

        $hash = md5($user->id . $user->password . $user->email . $newEmail);
        if ($hash !== $verifyCode) {
            throw new Exception\VerifyFailedException('ERR_USER_CHANGE_EMAIL_VERIFY_CODE_NOT_MATCH');
        }

        $user->email = $newEmail;
        if (!$user->save()) {
            throw new Exception\RuntimeException('ERR_USER_CHANGE_EMAIL_FAILED');
        }

        return $user;
    }

    /**
     * @param $mobile
     * @param $captcha
     * @param $userId
     * @return bool
     * @throws Exception\InvalidArgumentException
     * @throws Exception\UnauthorizedException
     */
    public static function bindMobile($mobile, $captcha, $userId)
    {

        /** @var Login $user */
        $user = Login::findFirst('id=' . $userId);
        if (!$user) {
            throw new Exception\UnauthorizedException('ERR_USER_NOT_EXIST');
        }

        if (!$user->mobileCaptchaCheck($mobile, $captcha)) {
            throw new Exception\InvalidArgumentException('ERR_USER_MOBILE_CAPTCHA_CHECK_FAILED');
        }
        $user->mobile = $mobile;
        $user->mobileStatus = 'active';
        $user->mobileConfirmedAt = time();
        $saved = $user->save();
        $user->login();
        return $saved;
    }

    /**
     * 验证手机验证码是否有效
     *
     * @param string $mobile 手机号
     * @param string $captcha 验证码
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    public function mobileCaptchaCheck($mobile, $captcha)
    {

        $cache = $this->getDI()->get('modelsCache');

        $cacheKey = 'sms_captcha_' . $mobile;
        if ($cache->exists($cacheKey)) {
            $data = $cache->get($cacheKey);
            if ($data['captcha'] == $captcha) {
                return true;
            }
        }
        throw new Exception\InvalidArgumentException('ERR_USER_MOBILE_CAPTCHA_CHECK_FAILED');
    }
}
