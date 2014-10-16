<?php

namespace Eva\EvaUser\Controllers;

use Eva\EvaUser\Models\Login;

class LogoutController extends ControllerBase
{
    public function indexAction()
    {
        $this->cookies->delete('realm');
        Login::removeBadges();
        $this->getDI()->get('session')->remove('auth-identity');
        $this->view->disable();

        return $this->response->redirect('/admin');
    }
}
