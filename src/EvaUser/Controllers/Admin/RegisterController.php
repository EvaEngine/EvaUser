<?php

namespace Eva\EvaUser\Controllers\Admin;

use Eva\EvaUser\Models;
use Eva\EvaUser\Forms;

class RegisterController extends ControllerBase
{
    public function indexAction()
    {
        $this->view->changeRender('admin/login/index');
        $this->dispatcher->forward(array(
            'namespace' => 'Eva\EvaUser\Controllers',
            'controller' => 'register',
            'action' => 'index',
        ));
    }

    public function checkAction()
    {
        $this->dispatcher->forward(array(
            'namespace' => 'Eva\EvaUser\Controllers',
            'controller' => 'register',
            'action' => 'check',
        ));
    }
}
