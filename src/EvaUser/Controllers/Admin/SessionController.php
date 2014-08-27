<?php

namespace Eva\EvaUser\Controllers\Admin;

use Eva\EvaUser\Models;
use Eva\EvaUser\Forms;

class SessionController extends ControllerBase
{
    public function verifyAction()
    {
        $this->dispatcher->forward(array(
            'namespace' => 'Eva\EvaUser\Controllers',
            'controller' => 'session',
            'action' => 'verify',
        ));
    }

    public function forgotAction()
    {
        $this->view->changeRender('admin/login/index');
        $this->dispatcher->forward(array(
            'namespace' => 'Eva\EvaUser\Controllers',
            'controller' => 'session',
            'action' => 'forgot',
        ));
    }

    public function resetAction()
    {
        $this->view->changeRender('admin/session/reset');
        $this->dispatcher->forward(array(
            'namespace' => 'Eva\EvaUser\Controllers',
            'controller' => 'session',
            'action' => 'reset',
        ));
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
