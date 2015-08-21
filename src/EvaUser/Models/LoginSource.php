<?php
/**
 * Wscn
 *
 * @link: https://github.com/wallstreetcn/wallstreetcn
 * @author: franktung<franktung@gmail.com>
 * @Date: 15/8/3
 * @Time: 下午1:44
 *
 */

namespace Eva\EvaUser\Models;


use Eva\EvaUser\Entities\LoginSources;

class LoginSource extends LoginSources
{
    public function recordSource($uid, $source, $loginAt)
    {
        $ipAddress = $_SERVER["REMOTE_ADDR"];
        $this->recordInfo($uid, $source, $loginAt, $ipAddress);
    }

    public function recordInfo($uid, $source, $loginAt, $ipAddress)
    {
        $loginSource = new self;
        $loginSource->userId = $uid;
        $loginSource->source = $source;
        $loginSource->loginAt = $loginAt;
        $loginSource->remoteIp = $ipAddress;
        $loginSource->save();
    }

} 