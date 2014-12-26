<?php

namespace Eva\EvaUser\Models;

use Eva\EvaUser\Entities;
use Phalcon\Mvc\Model\Message as Message;
use Eva\EvaEngine\Exception;
use Phalcon\DI;

class Login extends User
{
    //Session or cache keys for storage login user basic
    const AUTH_KEY_LOGIN = 'auth_identity';
    //Session or cache keys for storage login user roles
    const AUTH_KEY_ROLES = 'auth_roles';
    //Session or cache keys for storage login user token
    const AUTH_KEY_TOKEN = 'auth_token';

    //Cookie key for check user already login
    const LOGIN_COOKIE_KEY = 'evalogin';
    const LOGIN_COOKIE_REMEMBER_KEY = 'realm';

    //Login modes
    const LOGIN_MODE_SESSION = 'session';
    const LOGIN_MODE_TOKEN = 'token';

    const INFO_KEY_BADGE = 'badges';

    private $rememberMeTokenSalt = 'EvaUser_Login_TokenSalt';

    protected $rememberMeTokenExpires = 5184000; //60 days

    protected $maxLoginRetry = 5;

    protected static $loginMode = 'session';  //session or token

    public static function setLoginMode($mode)
    {
        Login::$loginMode = $mode;
    }

    public static function getLoginMode()
    {
        return Login::$loginMode;
    }

    /**
     * @param string $key
     * @param string|int $num 徽标数字，a 为不显示数字
     */
    public static function addBadge($key, $num = 'a')
    {
        /** @var \Phalcon\Session\AdapterInterface $storage */
        $storage = self::getAuthStorage();
        $badges = $storage->get(self::INFO_KEY_BADGE);
        if (is_null($badges)) {
            $badges = array();
        } else {
            $badges = (array)$badges;
        }
        $badges[$key] = $num;
        $storage->set(self::INFO_KEY_BADGE, $badges);
    }

    public static function removeBadge($key)
    {
        /** @var \Phalcon\Session\AdapterInterface $storage */
        $storage = self::getAuthStorage();
        $badges = $storage->get(self::INFO_KEY_BADGE);
        if (isset($badges[$key])) {
            unset($badges[$key]);
        }
        if (!$badges) {
            $storage->remove(self::INFO_KEY_BADGE);
        } else {
            $storage->set(self::INFO_KEY_BADGE, $badges);
        }
    }
    public static function removeBadges()
    {
        /** @var \Phalcon\Session\AdapterInterface $storage */
        $storage = self::getAuthStorage();
        $storage->remove(self::INFO_KEY_BADGE);
    }
    public static function getAuthStorage()
    {
        $di = DI::getDefault();
        if (Login::getLoginMode() == Login::LOGIN_MODE_SESSION) {
            return $di->getSession();
        }
        return $di->getTokenStorage();
    }

    public static function getCurrentUser()
    {
        $storage = Login::getAuthStorage();
        $currentUser = $storage->get(Login::AUTH_KEY_LOGIN);
        if ($currentUser) {
            $currentUser = (array)$currentUser;
            $currentUser['badges'] = $storage->get(self::INFO_KEY_BADGE);
            return $currentUser;
        }
        return array(
            'id' => 0,
            'username' => 'Guest',
            'status' => '',
            'email' => '',
            'screenName' => '',
            'avatar' => '',

        );
    }

    public static function getCurrentUserRoles()
    {
        $storage = Login::getAuthStorage();
        $roles = $storage->get(Login::AUTH_KEY_ROLES);
        if ($roles) {
            return $roles;
        }
        return array(
            'GUEST'
        );
    }

    public function getRememberMeTokenExpires()
    {
        return $this->rememberMeTokenExpires;
    }

    public function setRememberMeTokenExpire($rememberMeTokenExpires)
    {
        $this->rememberMeTokenExpires = $rememberMeTokenExpires;
        return $this;
    }

    public function getRememberMeHash(Entities\Users $userinfo)
    {
        //If user info changed, all user token will be unavailable
        return md5($this->rememberMeTokenSalt . $userinfo->username . $userinfo->email . $userinfo->status . $userinfo->password);
    }

    public function getRememberMeToken()
    {
        if (!$this->username) {
            $this->appendMessage(new Message('ERR_USER_REMEMBER_TOKEN_NO_USER_INPUT'));
            return false;
        }

        $sessionId = $this->getDI()->getSession()->getId();
        if (!$sessionId) {
            $this->appendMessage(new Message('ERR_USER_REMEMBER_TOKEN_NO_SESSION'));
            return false;
        }

        $userinfo = self::findFirst("username = '$this->username'");
        if (!$userinfo) {
            $this->appendMessage(new Message('ERR_USER_REMEMBER_TOKEN_USER_NOT_FOUND'));
            return false;
        }

        $token = new Entities\Tokens();
        $token->sessionId = $sessionId;
        $token->token = md5(uniqid(rand(), true));
        $token->userHash = $this->getRememberMeHash($userinfo);
        $token->userId = $userinfo->id;
        $token->refreshAt = time();
        $token->expiredAt = time() + $this->rememberMeTokenExpires;
        $token->save();
        $tokenString = $sessionId . '|' . $token->token . '|' . $token->userHash;

        return $tokenString;
    }


    public function saveUserToStorage(Entities\Users $userinfo)
    {
        $authIdentity = $this->userToAuthIdentity($userinfo);
        $storage = Login::getAuthStorage();
        $storage->set(Login::AUTH_KEY_LOGIN, $authIdentity);
        return $authIdentity;
    }

    public function userToAuthIdentity(Entities\Users $userinfo)
    {
        return array(
            'id' => $userinfo->id,
            'username' => $userinfo->username,
            'status' => $userinfo->status,
            'email' => $userinfo->email,
            'screenName' => $userinfo->screenName,
            'avatar' => $userinfo->getAvatar(),
        );
    }

    /**
     * System login
     * 1. Check user exsits
     * 2. Clear user login failde counter
     * 3. Update user last login time
     * 4. Save user info to Session
     *
     * @return Login
     */
    public function login()
    {
        $this->getDI()->getEventsManager()->fire('user:beforeLogin', $this);

        if (!$this->id) {
            throw new Exception\InvalidArgumentException('ERR_USER_NO_ID_INPUT');
        }

        $userinfo = array();
        if ($this->id) {
            $userinfo = self::findFirst("id = '$this->id'");
        }
        if (!$userinfo) {
            throw new Exception\UnauthorizedException('ERR_USER_NOT_EXIST');
        }

        if ($userinfo->status != 'active') {
            throw new Exception\UnauthorizedException('ERR_USER_NOT_ACTIVED');
        }

        $userinfo->failedLogins = 0;
        $userinfo->loginAt = time();
        $userinfo->save();

        $authIdentity = $this->saveUserToStorage($userinfo);
        if (Login::getLoginMode() == Login::LOGIN_MODE_SESSION) {
            $cookieDomain = $this->getDI()->getConfig()->user->loginCookieDomain;

            $cookies = $this->getDI()->getCookies()->set(Login::LOGIN_COOKIE_KEY, $userinfo->id);
            if ($cookieDomain) {
                $cookie = $cookies->get(Login::LOGIN_COOKIE_KEY);
                $cookie->setDomain($cookieDomain);
                $this->getDI()->getEventsManager()->attach(
                    'application:beforeSendResponse',
                    function($events, $application) use ($cookieDomain) {
                        $di = $application->getDI();
                        $sessionId = $di->getSession()->getId();
                        $application->getDI()->getCookies()->get('PHPSESSID')->setValue($sessionId)->setDomain($cookieDomain);
                    }
                );
            }
        }

        $this->getDI()->getEventsManager()->fire('user:afterLogin', $userinfo);
        return $userinfo;
    }

    /**
     * Login by Password
     *
     * @param $identify  username or email
     * @param $password  user password
     * @return Login
     */
    public function loginByPassword($identify, $password)
    {
        if (false === strpos($identify, '@')) {
            $this->assign(array(
                'username' => $identify,
                'password' => $password,
            ));
        } else {
            $this->assign(array(
                'email' => $identify,
                'password' => $password
            ));
        }
        $this->getDI()->getEventsManager()->fire('user:beforeLoginByPassword', $this);

        //Check password process
        $userinfo = array();
        if ($this->username) {
            $userinfo = self::findFirst("username = '$this->username'");
        } elseif ($this->email) {
            $userinfo = self::findFirst("email = '$this->email'");
        } else {
            throw new Exception\InvalidArgumentException('ERR_USER_NO_USERNAME_OR_EMAIL_INPUT');
        }

        if (!$userinfo) {
            throw new Exception\ResourceNotFoundException('ERR_USER_NOT_EXIST');
        }

        if ($userinfo->failedLogins >= $this->maxLoginRetry && $userinfo->loginFailedAt > (time() - 30)) {
            throw new Exception\RuntimeException('ERR_USER_PASSWORD_WRONG_MAX_TIMES');
        }

        $this->getDI()->getEventsManager()->fire('user:beforeVerifyPassword',
            array('user' => $this, 'userInDB' => $userinfo));
        if (!$userinfo->password) {
            throw new Exception\RuntimeException('ERR_USER_PASSWORD_EMPTY');
        }

        // check if hash of provided password matches the hash in the database
        if (!self::passwordVerify($this->password, $userinfo->password)) {
            //MUST be string type here
            $userinfo->failedLogins = (string)($userinfo->failedLogins + 1);
            $userinfo->loginFailedAt = time();
            $userinfo->save();
            throw new Exception\VerifyFailedException('ERR_USER_PASSWORD_WRONG');
        }
        $this->getDI()->getEventsManager()->fire('user:afterVerifyPassword',
            array('user' => $this, 'userInDB' => $userinfo));

        $login = new Login();
        $login->id = $userinfo->id;
        return $login->login();
    }

    public function loginByCookie($tokenString)
    {
        $this->getDI()->getEventsManager()->fire('user:beforeLoginByCookie', $tokenString);

        $tokenArray = explode('|', $tokenString);
        if (!$tokenArray || count($tokenArray) < 3) {
            $this->appendMessage(new Message('ERR_USER_REMEMBER_TOKEN_FORMAT_INCORRECT'));
            return false;
        }

        $token = new Entities\Tokens();
        $tokenInfo = $token::findFirst(array(
            "conditions" => "sessionId = :sessionId: AND token = :token: AND userHash = :userHash:",
            "bind" => array(
                'sessionId' => $tokenArray[0],
                'token' => $tokenArray[1],
                'userHash' => $tokenArray[2],
            )
        ));
        if (!$tokenInfo) {
            $this->appendMessage(new Message('ERR_USER_REMEMBER_TOKEN_NOT_FOUND'));
            return false;
        }

        if ($tokenInfo->expiredAt < time()) {
            $this->appendMessage(new Message('ERR_USER_REMEMBER_TOKEN_EXPIRED'));
            return false;
        }

        $userinfo = User::findFirst($tokenInfo->userId);
        $rememberMeHash = $this->getRememberMeHash($userinfo);
        //User changed status or password
        if ($rememberMeHash != $tokenInfo->userHash) {
            $this->appendMessage(new Message('ERR_USER_REMEMBER_TOKEN_ILLEGAL'));
            return false;
        }
        $login = new Login();
        $login->id = $tokenInfo->userId;
        $userinfo = $login->login();
        return $userinfo;
    }

    public function getAuthIdentity()
    {
        $authIdentity = $this->getDI()->getSession()->get(Login::AUTH_KEY_LOGIN);
        if ($authIdentity) {
            return $authIdentity;
        }
        return false;
    }

    /**
     * Returns the current state of the user's login
     * @return bool user's login status
     */
    public function isUserLoggedIn()
    {
        return $this->getAuthIdentity() ? true : false;
    }
}
