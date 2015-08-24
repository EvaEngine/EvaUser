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
    
    public function cleancookiesAction()
    {
        if (!empty($_COOKIE)) {
            $sessionDomain = $this->getDI()->getConfig()->session->cookie_params->domain;
            foreach ($_COOKIE as $key => $cookie) {
                $this->cookies->get($key)->setDomain($sessionDomain)->delete();
            }
        }

        return $this->response->redirect(eva_url('passport', '/login'));
    }
}
