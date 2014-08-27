<?php

namespace Eva\EvaUser\Controllers;

class LogoutController extends ControllerBase
{
    public function indexAction()
    {
        $this->cookies->delete('realm');
        $this->getDI()->get('session')->remove('auth-identity');
        $this->view->disable();

        return $this->response->redirect('/admin');
    }
}
