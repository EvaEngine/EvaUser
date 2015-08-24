<?php

namespace Eva\EvaUser\Controllers\Admin;

use Eva\EvaUser\Models;
use Eva\EvaUser\Models\Login;
use Eva\EvaUser\Forms;

class LoginController extends ControllerBase
{
    public function indexAction()
    {
        $next = eva_url('admin', '/admin/dashboard');
        if ($this->request->getHTTPReferer()) {
            $next = $this->request->getHTTPReferer();
        }

        return $this->response->redirect(eva_url('passport', '/login', [
            'next' => $next
        ]));
        $this->view->changeRender('admin/login/index');
        $this->dispatcher->setParam('loginSuccessRedirectUri', '/admin/dashboard');

        $this->dispatcher->forward(array(
            'namespace' => 'Eva\EvaUser\Controllers',
            'controller' => 'login',
            'action' => 'index',
        ));
    }

    public function reactiveAction()
    {
        $this->dispatcher->forward(array(
            'namespace' => 'Eva\EvaUser\Controllers',
            'controller' => 'login',
            'action' => 'reactive',
        ));
    }
}
