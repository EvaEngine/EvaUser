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
