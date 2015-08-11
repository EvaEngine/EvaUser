<?php
/**
 * Wscn
 *
 * @link: https://github.com/wallstreetcn/wallstreetcn
 * @author: franktung<franktung@gmail.com>
 * @Date: 15/8/11
 * @Time: 上午10:32
 *
 */

namespace Eva\EvaUser\Models;


use Eva\EvaUser\Entities\LoginRecords;

class LoginRecord extends LoginRecords
{
    public function recordSource($uid, $source, $loginAt)
    {
        $request = $this->getDI()->getService('request');
        dd($request);
        $ipAddress = $request->getServer('REMOTE_ADDR');
        $loginSource = new LoginSources;
        $loginSource->userId = $uid;
        $loginSource->source = $source;
        $loginSource->loginAt = $loginAt;
        $loginSource->remoteIp = $ipAddress;
        $loginSource->save();
        // log // throw exception
    }
} 