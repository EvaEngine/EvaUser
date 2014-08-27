<?php

namespace Eva\EvaUser\Controllers;

use Eva\EvaUser\Models;
use Eva\EvaUser\Forms;
use Eva\EvaUser\Models\Login;

class LoginController extends ControllerBase
{
    public function indexAction()
    {
        if (!$this->request->isPost()) {
            return;
        }

        if ($this->request->isAjax()) {
            $form = new Forms\LoginForm();
            if ($form->isValid($this->request->getPost()) === false) {
                return $this->showInvalidMessagesAsJson($form);
            }

            $user = new Login();
            try {
                $loginUser = $user->loginByPassword($this->request->getPost('identify'), $this->request->getPost('password'));
                if ($this->request->getPost('remember')) {
                    $token = $user->getRememberMeToken();
                    if ($token) {
                        $this->cookies->set(Login::LOGIN_COOKIE_REMEMBER_KEY, $token, time() + $user->getRememberMeTokenExpires());
                    }
                }
                return $this->showResponseAsJson(Login::getCurrentUser());
            } catch (\Exception $e) {
                return $this->showExceptionAsJson($e, $user->getMessages());
            }

        } else {
            $form = new Forms\LoginForm();
            if ($form->isValid($this->request->getPost()) === false) {
                $this->showInvalidMessages($form);
                return $this->redirectHandler($this->getDI()->getConfig()->user->loginFailedRedirectUri, 'error');
            }

            $user = new Login();
            try {
                $user->loginByPassword($this->request->getPost('identify'), $this->request->getPost('password'));
                if ($this->request->getPost('remember')) {
                    $token = $user->getRememberMeToken();
                    if ($token) {
                        $this->cookies->set('realm', $token, time() + $user->getRememberMeTokenExpires());
                    } else {
                        $this->flashSession->error($user->getMessages());
                    }
                }
                //$this->flashSession->success('SUCCESS_USER_LOGGED_IN');
                return $this->redirectHandler($this->getDI()->getConfig()->user->loginSuccessRedirectUri);
            } catch (\Exception $e) {
                $this->showException($e, $user->getMessages());
                return $this->redirectHandler($this->getDI()->getConfig()->user->loginFailedRedirectUri, 'error');
            }

        }
    }

    public function reactiveAction()
    {
        $username = $this->request->get('username');
        if ($this->request->isAjax()) {
            if (!$username) {
                return $this->showErrorMessageAsJson(400, 'ERR_USER_REACTIVE_NO_USERNAME_INPUT');
            }
            $user = new Models\Register();
            try {
                $user->sendVerificationEmail($username);
                return $this->showResponseAsJson('SUCCESS_USER_ACTIVE_MAIL_SENT');
            } catch (\Exception $e) {
                return $this->showExceptionAsJson($e, $user->getMessages());
            }

        } else {
            if (!$username) {
                return $this->redirectHandler($this->getDI()->getConfig()->user->resetFailedRedirectUri);
            }
            $user = new Models\Register();
            try {
                $user->sendVerificationEmail($username);
                $this->flashSession->success('SUCCESS_USER_ACTIVE_MAIL_SENT');
                return $this->redirectHandler($this->getDI()->getConfig()->user->resetSuccessRedirectUri);
            } catch (\Exception $e) {
                $this->showException($e, $user->getMessages());
                return $this->redirectHandler($this->getDI()->getConfig()->user->resetFailedRedirectUri);
            }

        }
    }
}
