<?php

namespace Eva\EvaUser\Controllers\Admin;

class LogoutController extends ControllerBase
{
    public function indexAction()
    {
        $this->view->disable();
        $this->cookies->delete('realm');
        $this->getDI()->get('session')->remove('auth-identity');

        return $this->response->redirect('/admin');
    }
}
