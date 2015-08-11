<?php
/**
 * Wscn
 *
 * @link: https://github.com/wallstreetcn/wallstreetcn
 * @author: franktung<franktung@gmail.com>
 * @Date: 15/8/4
 * @Time: 下午5:53
 *
 */

namespace Eva\EvaUser\Events;

use Eva\EvaOAuthClient\Models\OAuthManager;
use Eva\EvaUser\Models\LoginSource;
use Eva\EvaUser\Models\Register;
use Eva\EvaUser\Models\Login;
use Phalcon\Events\Event;
use Eva\EvaEngine\IoC;


class UserListener
{

    public function afterLogin(Event $event, Login $user)
    {
        // 用户未更新过资料并且是 session 模式（web）
        if ((!$user->profile || !$user->profile->updatedAt) && Login::getLoginMode() == Login::LOGIN_MODE_SESSION) {
//            /** @var \Phalcon\Http\RequestInterface, $httpRequest */
//            $httpRequest = IoC::get('request');
//            if ($httpRequest->isAjax() || $httpRequest->getQuery('ajax')) {
            $user->addBadge('profile');
//            } else {
//                /** @var \Phalcon\Http\ResponseInterface, $httpResponse */
//                $httpResponse = IoC::get('response');
//                $httpResponse->redirect(IoC::get('config')->baseUri . '/tasks/profile');
//                $httpResponse->send();
//                exit();
//            }
        }

        // 记录登录用户的登录来源
        $source = $this->getLoginSource();
        if($source) {
            $loginSource = new LoginSource();
            $loginSource->recordSource($user->id, $source, $user->loginAt);
        }
    }

    public function getLoginSource()
    {
        $request = $request = new \Phalcon\Http\Request();
        $data = $request->getRawBody();
        $data = json_decode($data, true);
        if (isset($data['source'])) {   // api json数据
            $source = $data['source'];
        } elseif ($request->getPost('source')) {  // 表单
            $source = $request->getPost('source');
        } elseif (isset($_SERVER['HTTP_HOST'])) {
            $refer = $_SERVER['HTTP_HOST'];   //HTTP_REFERER来判断
            $config = Ioc::getDI()->getConfig();
            $xgbDomain = $config->domains->stock->domain;
            if (strstr($xgbDomain, $refer)) {
                $source = 'xgb';
            }
        }
        return $source;
    }
    public function beforeRegister(Event $event, $params)
    {
        $source = $this->getSource();
        $providerType = $this->getProviderType();
        $params->providerType = $providerType;
        $params->source = $source;
    }

    public function afterRegister(Event $event, $params)
    {

    }

    public function getSource()
    {
        $request = new \Phalcon\Http\Request();
        $data = $request->getRawBody();
        if($data) {
            $data = json_decode($data, true);
            $source = $data['source'];
        } elseif ($request->getPost('source')) {
            $source = $request->getPost('source');
        } else {
            $source = '';
        }
        return $source;
    }

    public function getProviderType()
    {
        $request = new \Phalcon\Http\Request();
        $data = $request->getRawBody();
        $host = $_SERVER['HTTP_HOST'];
        $config = IoC::getDI()->getConfig();
        $main = $config->domains->main->domain;
        $api = $config->domains->api->domain;
        $admin = $config->domains->wscnAdmin->domain;
        $accessToken = OAuthManager::getAccessToken();
        if (strstr($main, $host)) {
            // web端
            if ($request->getPost('email')) {
                $provider = "web_registration_email";
            } elseif ($request->getPost('mobile')) {
                $provider = "web_registration_mobile";
            }
        } elseif (strstr($api, $host)) {
            // app端
            if ($data) {
                $data = json_decode($data, true);
                if (isset($data['mobile'])) {
                    $provider = "app_registration_mobile";
                } elseif (isset($data['email'])) {
                    $provider = "app_registration_email";
                }
            }
        } elseif (strstr($admin, $host)) {
            // admin
            $provider = 'admin';
        } elseif(isset($accessToken)) {
            // oauth
            $provider = 'web_'.$accessToken['adapterKey'] . '_' . $accessToken['version'];
        }
        return $provider;
    }
} 