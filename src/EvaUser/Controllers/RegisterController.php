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
            $transaction = $this->getDI()->getTransactions()->get();
            try {
                $transaction->begin();
                $registerUser = $user->register();
                $transaction->commit();
                return $this->showResponseAsJson($registerUser);
            } catch (\Exception $e) {
                $transaction->rollback();
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

    public function checkAction()
    {
        $username = $this->request->get('username');
        $email = $this->request->get('email');

        if ($username) {
            $userinfo = Models\Login::findFirst(array("username = '$username'"));
        } elseif ($email) {
            $userinfo = Models\Login::findFirst(array("email = '$email'"));
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
}
