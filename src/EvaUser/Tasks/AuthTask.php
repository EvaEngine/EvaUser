<?php
namespace Eva\EvaUser\Tasks;

use Eva\EvaEngine\Tasks\TaskBase;
use Phalcon\Mvc\Model\Query;
use Eva\EvaUser\Entities\RealnameAuth;
use Eva\EvaUser\Controllers\ValidateController;
use Eva\EvaUser\Entities\UserAuthLogs;
use WscnFinance\Remote\User;

class AuthTask extends TaskBase {

    public function indexAction() {
        $userData = RealnameAuth::find(array('status=4', 'limit' => 200));
        $idArray = array();
        if ($userData) {
            foreach ($userData as $k => $v) {
                $id['Name'] = $v->realName;
                $id['CardNum'] = $v->cardNum;
                $idArray[] = $id;
            }

            $val = new ValidateController();
            $xmlString = $val->getData($idArray);
            $thread = new UserAuthLogs();

            // file_put_contents('./aaa.txt', $xmlString );
            $obj = (array)simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);
            $arr=array();
            if ($obj['message']->status == 0) { //本次请求成功
                foreach ($obj['policeCheckInfos'] as $v) {
                    if ($v->message->status == 0) { //这次身份证可以验证
                        $arr["$v->compStatus"].= (string)$v->identitycard . ',';
                    }
                }
                $auth = new RealnameAuth();
                foreach ($arr as $key => $value) {
                    $cardNums = substr($value, 0, -1);
                    $sql = "update eva_user_realname_auth set status={$key} where cardNum in ({$cardNums})";
                    $stars = $auth->getWriteConnection()->query($sql);
                }
            }
            $thread->requestTime = time();
            $thread->quantity = $userData->count();
            $thread->status = $obj['message']->status;
            $thread->save();
        }//empty($userData)
    }
}
