<?php
/**
 * Created by PhpStorm.
 * User: yudoudou
 * Date: 15/8/5
 * Time: 上午10:52
 */

namespace Eva\EvaUser\Models\Validator;

use Eva\EvaEngine\IoC;
use Eva\EvaUser\Models\Des;
use Phalcon\Mvc\Model\Validator;

/**
 * 通过接口进行实名认证
 * Class ValidRealName
 */
class ValidRealName extends Validator
{
    public function validate($model)
    {
        $realNameField = $this->getOption('realNameField') ?: 'realName';
        $cardNumField = $this->getOption('cardNumField') ?: 'cardNum';
        $isSuccess = false;

        $xmlString = $this->requestApi([
            ['Name' => $model->$realNameField, 'CardNum' => $model->$cardNumField]
        ]);
        $obj = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);

        if ($obj->message->status == 0) {
            $authResult = $obj->policeCheckInfos->policeCheckInfo;
            // 等于3代表正确，硬编码到这里
            $isSuccess = intval($authResult->compStatus) === 3;
        }
        if (!$isSuccess) {
            $messageStr = $this->getOption('message') ?: '请输入有效的名字和身份证号码';
            $this->appendMessage($messageStr, $realNameField . '&' . $cardNumField, 'ValidRealName');
        }
        return $isSuccess;
    }

    /**
     * 请求第三方进行认证
     * <code>
     * $this->requestApi([['Name' => 'xx', 'CardNum' => 'xx']])
     * </code>
     * @param array $identities
     * @return string
     */
    private function requestApi(array $identities)
    {
        $config = IoC::get('config')->validateConfig;
        $soap = new \SoapClient($config->wsdlURL, ['connection_timeout' => 15]);
        $des = new Des($config->Key, $config->iv);
        $partner = $des->encrypt($config->partner);
        $partnerPW = $des->encrypt($config->partnerPW);
        $type = $des->encrypt($config->type);

        $userData = [];
        foreach ($identities as $one) {
            $userData[] = $this->formatParam($config->type, $one);
        }
        $userData = implode(';', $userData);

        //先将中文转码
        $userData = mb_convert_encoding($userData, "GBK", "UTF-8");
        $userData = $des->encrypt($userData);
        $params = array("userName_" => $partner, "password_" => $partnerPW, "type_" => $type, "param_" => $userData);
        //请求查询
        $method = count($identities) > 1 ? 'queryBatch' : 'querySingle';
        $returnValueKey = count($identities) > 1 ? 'queryBatchReturn' : 'querySingleReturn';
        $data = $soap->$method($params);
        $resultXML = $des->decrypt($data->$returnValueKey);
        $resultXML = mb_convert_encoding($resultXML, "UTF-8", "GBK");
        return $resultXML;
    }

    /**
     * 格式化参数
     * @param array $params
     * @return string
     */
    private function formatParam($queryType, $params)
    {
        $supportClass = IoC::get('config')->validateConfig->supportClass;
        if (empty($supportClass[$queryType])) {
            return -1;
        }
        $keys = array();
        $values = array();

        foreach ($params as $key => $value) {
            $keys[] = $key;
            $values[] = $value;
        }
        $param = str_replace($keys, $values, $supportClass[$queryType]);
        return $param;
    }
}
