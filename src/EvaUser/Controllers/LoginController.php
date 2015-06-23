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

        if ($this->request->isAjax() || $this->request->get('ajax')) {
            $form = new Forms\LoginForm();
            if ($form->isValid($this->request->getPost()) === false) {
                return $this->showInvalidMessagesAsJson($form);
            }

            $user = new Login();
            try {
                $loginUser = $user->loginByPassword($this->request->getPost('identify'),
                    $this->request->getPost('password'));
                $cookieDomain = $this->getDI()->getConfig()->session->sso_domain;
                if ($loginUser->id && $this->request->getPost('remember')) {
                    $token = $user->getRememberMeToken();
                    if ($token) {
                        $cookies = $this->cookies->set(Login::LOGIN_COOKIE_REMEMBER_KEY, $token,
                            time() + $user->getRememberMeTokenExpires());
                        if ($cookieDomain) {
                            $cookie = $cookies->get(Login::LOGIN_COOKIE_REMEMBER_KEY);
                            $cookie->setDomain($cookieDomain);
                        }
                    }
                }

                if (!empty($_SERVER['HTTP_ORIGIN'])) {
                    $this->response->setHeader('Access-Control-Allow-Credentials', 'true');
                    $this->response->setHeader('Access-Control-Allow-Origin', $_SERVER['HTTP_ORIGIN']);
                    $this->response->setHeader('Access-Control-Allow-Methods', 'POST');
                    $this->response->setHeader('Access-Control-Allow-Headers',
                        'Content-Type, Authorization, X-Requested-With');
                }

                return $this->showResponseAsJson(Login::getCurrentUser());
            } catch (\Exception $e) {
                return $this->showExceptionAsJson($e, $user->getMessages());
            }

        } else {
            $loginFailedRedirectUri = $this->dispatcher->getParam('loginFailedRedirectUri');

            $loginFailedRedirectUri = $loginFailedRedirectUri ? $loginFailedRedirectUri : $this->getDI()->getConfig()->user->loginFailedRedirectUri;
            $loginFailedRedirectUri = $loginFailedRedirectUri ? $loginFailedRedirectUri : $this->request->getURI();
            $form = new Forms\LoginForm();
            if ($form->isValid($this->request->getPost()) === false) {
                $this->showInvalidMessages($form);

                return $this->redirectHandler($loginFailedRedirectUri, 'error');
            }

            $user = new Login();
            try {
                $user->loginByPassword($this->request->getPost('identify'), $this->request->getPost('password'));
                if ($this->request->getPost('remember')) {
                    $token = $user->getRememberMeToken();
                    if ($token) {
                        $ssoDomain = $this->getDI()->getConfig()->session->sso_domain;

                        $this->cookies->set('realm', $token, time() + $user->getRememberMeTokenExpires());
                         if ($ssoDomain) {
                            $cookie = $this->cookies->get(Login::LOGIN_COOKIE_REMEMBER_KEY);
                            $cookie->setDomain($ssoDomain);
                        }
                    } else {
                        $this->flashSession->error($user->getMessages());
                    }
                }
                //$this->flashSession->success('SUCCESS_USER_LOGGED_IN');
                $loginSuccessRedirectUri = $this->dispatcher->getParam('loginSuccessRedirectUri');
                if (empty($loginSuccessRedirectUri)) {
                    $loginSuccessRedirectUri = '/';
                }

                return $this->response->redirect($loginSuccessRedirectUri);
            } catch (\Exception $e) {
                $this->showException($e, $user->getMessages());

                // $this->getDI()->getConfig()->user->loginFailedRedirectUri
                return $this->response->redirect($loginFailedRedirectUri, 'error');
            }

        }
    }

    public function reactiveAction()
    {
        $identify = $this->request->get('identify');
        if ($this->request->isAjax() || $this->request->get('ajax')) {
            if (!$identify) {
                return $this->showErrorMessageAsJson(400, 'ERR_USER_REACTIVE_NO_USERNAME_INPUT');
            }
            $user = new Models\Register();
            try {
                $user->sendVerificationEmail($identify);

                return $this->showResponseAsJson('SUCCESS_USER_ACTIVE_MAIL_SENT');
            } catch (\Exception $e) {
                return $this->showExceptionAsJson($e, $user->getMessages());
            }

        } else {
            if (!$identify) {
                return $this->redirectHandler($this->getDI()->getConfig()->user->resetFailedRedirectUri);
            }
            $user = new Models\Register();
            try {
                $user->sendVerificationEmail($identify);
                $this->flashSession->success('SUCCESS_USER_ACTIVE_MAIL_SENT');

                return $this->redirectHandler($this->getDI()->getConfig()->user->resetSuccessRedirectUri);
            } catch (\Exception $e) {
                $this->showException($e, $user->getMessages());

                return $this->redirectHandler($this->getDI()->getConfig()->user->resetFailedRedirectUri);
            }

        }
    }
}
