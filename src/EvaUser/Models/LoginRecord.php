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


use Eva\EvaEngine\Exception\RuntimeException;
use Eva\EvaEngine\IoC;
use Eva\EvaUser\Entities\LoginRecords;

class LoginRecord extends LoginRecords
{
    public function recordSource($uid, $source, $loginAt)
    {
        $request = $this->getDI()->getShared('request');
        $ipAddress = $request->getServer('REMOTE_ADDR');
        $this->userId = $uid;
        $this->source = $source;
        $this->loginAt = $loginAt;
        $this->remoteIp = $ipAddress;
        if ($this->save() == false) {
            foreach ($this->getMessages() as $message) {
                throw new RuntimeException($message, 500);
            }
        }

    }

    public static function getSourceOfUser()
    {
        $request = Ioc::getDI()->getShared('request');
        $data = $request->getRawBody();
        if (isset($data)) {
            $data = json_decode($data, true);
        }
        $source = '';
        $refer = $request->getServer('HTTP_REFERER');
        $host = $request->getServer('HTTP_HOST');
        if (isset($data['source'])) {   // api json数据
            $source = $data['source'];
        } elseif ($request->getPost('source')) {  // 表单填了source字段
            $source = $request->getPost('source');
        } elseif (isset($refer)) {  // HTTP_REFERER来判断
            $source = self::checkLoginSourceOfUser($refer);
        } elseif (isset($host)) {   // 通过HTTP_HOST来尝试判断
            $source = self::checkLoginSourceOfUser($host);
        } else {
            $source = 'DEFAULT';
        }
        return $source;
    }

    public static function checkLoginSourceOfUser($target)
    {
        $loginSource = 'DEFAULT';
        $config = IoC::getDI()->getConfig();
        $sourceConfig = $config->user->source;
        foreach ($sourceConfig as $key => $source) {
            if(strstr($target, $key)) {
                $loginSource = $source;
            }
        }
        return $loginSource;

    }
} 