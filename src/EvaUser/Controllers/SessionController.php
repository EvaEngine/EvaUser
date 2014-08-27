<?php

namespace Eva\EvaUser\Controllers;

use Eva\EvaUser\Models;
use Eva\EvaUser\Forms;

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

        $email = $this->request->getPost('email');
        if ($this->request->isAjax()) {
            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->showErrorMessageAsJson(401, 'ERR_EMAIL_FORMAT_NOT_CORRECT');
            }
            $user = new Models\ResetPassword();
            $user->assign(array(
                'email' => $email,
            ));
            try {
                $user->requestResetPassword();
                return $this->showResponseAsJson('SUCCESS_USER_RESET_MAIL_SENT');
            } catch (\Exception $e) {
                return $this->showExceptionAsJson($e, $user->getMessages());
            }
        } else {
            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->redirectHandler($this->getDI()->getConfig()->user->resetFailedRedirectUri);
            }
            $user = new Models\ResetPassword();
            $user->assign(array(
                'email' => $email,
            ));
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

        $user->assign(array(
            'username' => $username,
            'password' => $this->request->getPost('password'),
        ));
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
