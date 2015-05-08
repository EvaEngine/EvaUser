<?php
namespace Eva\EvaUser\TokenStorage;

use Eva\EvaEngine\IoC;
use Eva\EvaEngine\Service\TokenStorage;

/**
 * Created by PhpStorm.
 * User: wscn
 * Date: 15/5/7
 * Time: 下午9:05
 */
class SessionTokenStorage extends TokenStorage
{
    static protected $instance = null;

    static public function getInstance()
    {
        $config = IoC::get('config');
        if (self::$instance == null) {
            self::$instance = new static($config->tokenStorage->toArray());
        }

        return self::$instance;
    }

    public function getId()
    {
        if ($this->tokenId) {
            $this->setId($this->generateId());
        }

        return $this->tokenId;
    }

    protected function generateId()
    {
        $sso_ticket_name = IoC::get('config')->session->sso_ticket_name;
        if (!empty($_COOKIE[$sso_ticket_name])) {
            return $_COOKIE[$sso_ticket_name];
        }

        return '';
    }
}