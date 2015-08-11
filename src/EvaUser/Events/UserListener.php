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

use Eva\EvaUser\Models\LoginRecord;
use Eva\EvaUser\Models\Login;
use Eva\EvaOAuthClient\Models\OAuthManager;
use Eva\EvaUser\Models\Register;
use Phalcon\Events\Event;
use Eva\EvaEngine\IoC;


class UserListener
{
    public function afterLogin(Event $event, Login $user)
    {
        // 记录登录用户的登录来源
        $loginSource = new LoginRecord();  //拆分        loginHistory，还表的命名
        $source = $loginSource->getSourceOfUser();
        if($source) {
            $loginSource->recordSource($user->id, $source, $user->loginAt);
        }
    }
} 