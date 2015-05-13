<?php

namespace Eva\EvaUser\Controllers;

use Eva\EvaUser\Models;
use Eva\EvaUser\Forms;
use Eva\EvaEngine\Exception;

class SessionController extends ControllerBase
{
    public function verifyAction()
    {
        $code = $this->dispatcher->getParam('code');
        $username = $this->dispatcher->getParam('username');
        $user = new Models\Register();

        try {
            $user->verifyNewUser($username, $code);
        } catch (\Exception $e) {
            $this->showException($e, $user->getMessages());

            return $this->response->redirect($this->getDI()->getConfig()->user->activeFailedRedirectUri);
        }
        $this->flashSession->success('SUCCESS_USER_ACTIVED');

        return $this->response->redirect($this->getDI()->getConfig()->user->activeSuccessRedirectUri);
    }

    public function forgotAction()
    {
        if (!$this->request->isPost()) {
            return;
        }

        $email = $this->request->getPost('identify');
        if ($this->request->isAjax() || $this->request->get('ajax')) {
            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->showErrorMessageAsJson(401, 'ERR_EMAIL_FORMAT_NOT_CORRECT');
            }
            $user = new Models\ResetPassword();
            $user->assign(
                array(
                    'email' => $email,
                )
            );
            try {
                $user->requestResetPassword();

                return $this->showResponseAsJson('SUCCESS_USER_RESET_MAIL_SENT');
            } catch (\Exception $e) {
                // 邮箱未激活，直接发送验证邮件
                if ($e->getCode() == 1000) {
                    $registerUser = new Models\Register();
                    $registerUser->sendVerificationEmail($email);
                }

                return $this->showExceptionAsJson($e, $user->getMessages());
            }
        } else {
            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->redirectHandler($this->getDI()->getConfig()->user->resetFailedRedirectUri);
            }
            $user = new Models\ResetPassword();
            $user->assign(
                array(
                    'email' => $email,
                )
            );
            try {
                $user->requestResetPassword();
                $this->flashSession->success('SUCCESS_USER_RESET_MAIL_SENT');
            } catch (\Exception $e) {
                $this->showException($e, $user->getMessages());

                return $this->redirectHandler($this->getDI()->getConfig()->user->resetFailedRedirectUri);
            }

            return $this->redirectHandler($this->getDI()->getConfig()->user->resetSuccessRedirectUri);
        }
    }

    public function sendResetCaptchaAction()
    {
        $mobile = $this->request->getPost('mobile');
        $registerModel = new Models\Register();
        $registerModel->mobileCaptcha($mobile, $type = 1);
        $data = array('mobile' => $mobile, 'timestamp' => time());

        return $this->showResponseAsJson($data);
    }

    public function resetByMobileAction()
    {
        $mobile = $this->request->getPost('mobile');
        $captcha = $this->request->getPost('captcha');
        /** @var \Eva\EvaUser\Models\ResetPassword $user */
        $user = Models\User::findFirst("mobile='{$mobile}' AND mobileStatus='active'");
        if (!$user) {
            throw new Exception\ResourceNotFoundException('ERR_USER_NOT_EXIST');
        }
        if ($user->mobileStatus != 'active') {
            throw new Exception\InvalidArgumentException('ERR_MOBILE_INACTIVATED');
        }
        try {
            $user->mobileCaptchaCheck($mobile, $captcha);
        } catch(\Exception $e) {
            return $this->showExceptionAsJson($e);
        }
        $resetPassword = new Models\ResetPassword();


        $form = new Forms\MobileResetPasswordForm();
        if ($form->isValid($this->request->getPost()) === false) {
            return $this->showInvalidMessagesAsJson($form);
        }

        $resetPassword->assign(
            array(
                'username' => $user->username,
                'password' => $this->request->getPost('password'),
            )
        );
        try {
            $resetPassword->resetPassword();
            $this->flashSession->success('SUCCESS_USER_PASSWORD_RESET');
        } catch (\Exception $e) {
            return $this->showExceptionAsJson($e, $user->getMessages());
        }

        return $this->showResponseAsJson('SUCCESS_USER_PASSWORD_RESET');

    }

    public function resetAction()
    {
        $code = $this->dispatcher->getParam('code');
        $username = $this->dispatcher->getParam('username');
        $user = new Models\ResetPassword();
        try {
            $user->verifyPasswordReset($username, $code);
        } catch (\Exception $e) {
            $this->showException($e, $user->getMessages());

            return $this->redirectHandler($this->getDI()->getConfig()->user->resetFailedRedirectUri);
        }

        if (!$this->request->isPost()) {
            return;
        }

        $form = new Forms\ResetPasswordForm();
        if ($form->isValid($this->request->getPost()) === false) {
            $this->showInvalidMessages($form);

            return $this->redirectHandler($this->getDI()->getConfig()->user->resetFailedRedirectUri);
        }

        $user->assign(
            array(
                'username' => $username,
                'password' => $this->request->getPost('password'),
            )
        );
        try {
            $user->resetPassword();
            $this->flashSession->success('SUCCESS_USER_PASSWORD_RESET');
        } catch (\Exception $e) {
            $this->showException($e, $user->getMessages());

            return $this->redirectHandler($this->getDI()->getConfig()->user->resetFailedRedirectUri, 'error');
        }

        return $this->redirectHandler($this->getDI()->getConfig()->user->resetSuccessRedirectUri);
    }

    public function changemailAction()
    {
        $code = $this->dispatcher->getParam('code');
        $username = $this->dispatcher->getParam('username');
        $email = $this->dispatcher->getParam('email');
        $user = new Models\User();

        try {
            $user->changeEmail($username, $email, $code);
            $this->flash->success('SUCCESS_USER_EMAIL_CHANGED');
        } catch (\Exception $e) {
            $this->showException($e, $user->getMessages());
        }
    }

    public function testAction()
    {
        $user = new Models\Login();
        $authIdentity = $user->getAuthIdentity();
        if (!$authIdentity && ($tokenString = $this->cookies->get('realm')->getValue())) {
            if ($user->loginByCookie($tokenString)) {
            } else {
                $this->cookies->delete('realm');
            }
        }
    }
}
