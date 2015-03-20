<?php

namespace Eva\EvaUser\Controllers;

use Eva\EvaUser\Models;
use Eva\EvaUser\Forms;

class RegisterController extends ControllerBase
{
    public function indexAction()
    {
        if (!$this->request->isPost()) {
            return;
        }

        if ($this->request->isAjax() || $this->request->get('ajax')) {
            $form = new Forms\RegisterForm();
            if ($form->isValid($this->request->getPost()) === false) {
                return $this->showInvalidMessagesAsJson($form);
            }
            $user = new Models\Register();
            $user->assign(array(
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'password' => $this->request->getPost('password'),
            ));
            try {
                $registerUser = $user->register();
                return $this->showResponseAsJson($registerUser);
            } catch (\Exception $e) {
                return $this->showExceptionAsJson($e, $user->getMessages());
            }
        } else {
            $form = new Forms\RegisterForm();
            if ($form->isValid($this->request->getPost()) === false) {
                $this->showInvalidMessages($form);
                return $this->redirectHandler($this->getDI()->getConfig()->user->registerFailedRedirectUri, 'error');
            }
            $user = new Models\Register();
            $user->assign(array(
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'password' => $this->request->getPost('password'),
            ));

            try {
                $user->register();
            } catch (\Exception $e) {
                $this->showException($e, $user->getMessages());
                return $this->redirectHandler($this->getDI()->getConfig()->user->registerFailedRedirectUri, 'error');
            }
            $this->flashSession->success('SUCCESS_USER_REGISTERED_ACTIVE_MAIL_SENT');
            return $this->redirectHandler($this->getDI()->getConfig()->user->registerSuccessRedirectUri);
        }
    }

    public function mobileAction()
    {
        if (!$this->request->isPost()) {
            return;
        }

        if ($this->request->isAjax() || $this->request->get('ajax')) {
            $form = new Forms\MobileRegisterForm();
            if ($form->isValid($this->request->getPost()) === false) {
                return $this->showInvalidMessagesAsJson($form);
            }
            $user = new Models\Register();
            $user->assign(array(
                'mobile' => $this->request->getPost('mobile'),
                'password' => $this->request->getPost('password'),
            ));
            try {
                $registerUser = $user->registerByMobile();
                return $this->showResponseAsJson($registerUser);
            } catch (\Exception $e) {
                return $this->showExceptionAsJson($e, $user->getMessages());
            }
        } else {
            $form = new Forms\MobileRegisterForm();
            if ($form->isValid($this->request->getPost()) === false) {
                $this->showInvalidMessages($form);
                return $this->redirectHandler($this->getDI()->getConfig()->user->registerFailedRedirectUri, 'error');
            }
            $user = new Models\Register();
            $user->assign(array(
                'mobile' => $this->request->getPost('mobile'),
                'password' => $this->request->getPost('password'),
            ));

            try {
                $user->registerByMobile();
            } catch (\Exception $e) {
                $this->showException($e, $user->getMessages());
                return $this->redirectHandler($this->getDI()->getConfig()->user->registerFailedRedirectUri, 'error');
            }
            $this->flashSession->success('SUCCESS_USER_REGISTERED_ACTIVE_MAIL_SENT');
            return $this->redirectHandler($this->getDI()->getConfig()->user->registerSuccessRedirectUri);
        }
    }

    public function codeAction()
    {

        $mobile = $this->request->getPost('mobile');

        $cache = $this->getDI()->get('modelsCache');

        $cacheKey = 'sms_code_' . $mobile;

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
        $code = mt_rand(100000, 999999);
        $templateId = $this->getDI()->getConfig()->smsSender->templates->verifyCode;
        $result = $sender->sendTemplateMessage($mobile, $templateId, ['number' => $code]);

        $data['timestamp'] = $now;
        $data['code'] = $code;

        //一小时内有效
        $cacheTime = 1 * 60 * 60;
        $cache->save($cacheKey, $data, $cacheTime);


        return $this->showResponseAsJson($result);
    }

    public function codeCheckAction()
    {
        $mobile = $this->request->getPost('mobile');
        $code = $this->request->getPost('code');

        $cache = $this->getDI()->get('modelsCache');

        $cacheKey = 'sms_code_' . $mobile;

        $result = false;
        if ($cache->exists($cacheKey)) {
            $data = $cache->get($cacheKey);

            if ($data['code'] == $code) {
                $result = true;
            }
        }

        return $this->showResponseAsJson($result);
    }

    public function checkAction()
    {
        $username = $this->request->get('username');
        $email = $this->request->get('email');
        $mobile = $this->request->get('mobile');

        if ($this->hasQQ($username)) {
            $this->response->setStatusCode('409', 'User Already Exists');
        }

        $loginedUser = Models\Login::getCurrentUser();
        $extraCondition = '';
        // 已登录用户表示当前为修改用户名，允许与当前用户名相同
        if ($loginedUser['id'] > 0) {
            $extraCondition .= ' AND id != ' . $loginedUser['id'];
        }
        if ($username) {
            $userinfo = Models\Login::findFirst(array("username = '$username' {$extraCondition}"));
        } elseif ($email) {
            $userinfo = Models\Login::findFirst(array("email = '$email' {$extraCondition}"));
        } elseif ($mobile) {
            $userinfo = Models\Login::findFirst(array("mobile = '$mobile' {$extraCondition}"));
        } else {
            $userinfo = array();
        }
        $this->view->disable();
        if ($userinfo) {
            $this->response->setStatusCode('409', 'User Already Exists');
        }

        return $this->response->setJsonContent(array(
            'exist' => $userinfo ? true : false,
            'id' => $userinfo ? $userinfo->id : 0,
            'status' => $userinfo ? $userinfo->status : null,
        ));
    }

    /**
     * 禁止用户名中含QQ号
     * @param $username
     * @return bool
     */
    public function hasQQ($username)
    {
        if ($username) {
            $pos = stripos($username, 'q');
            if ($pos !== false) {
                $num = preg_match_all('/\d/', $username);
                if ($num > 6) {
                    return true;
                }
            }
        }

        return false;
    }
}
