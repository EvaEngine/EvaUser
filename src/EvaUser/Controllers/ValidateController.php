<?php
/**
 * @desc 综合业务平台--查询 API
 * @author harvey
 * @since 2010-11-20
 *
 */
namespace Eva\EvaUser\Controllers;

use Eva\EvaUser\Models;
use Eva\EvaUser\Forms;
use Eva\EvaUser\Models\Login;
use Eva\EvaUser\Models\Des;
class ValidateController extends ControllerBase
{
    /**
     * @param string $param 查询参数
     * @return string
     */
    function getData($param) {
        $config = $this->getDI()->getConfig()->validateConfig;
        try {
            $soap = new \SoapClient($config->wsdlURL,array("connection_timeout" => 15));
        }
        catch(Exception $e) {
            return "Linkerror";
        }
        $DES = new Des ( $config->Key, $config->iv );
        //@todo 加密数据
        $partner = $DES->encrypt($config->partner);
        $partnerPW = $DES->encrypt($config->partnerPW);
        $type = $DES->encrypt($config->type);
        $userData='';
        if(count($param) > 1){
            foreach ($param as $k => $v) {
                $userData .= $this->formatParam($config->type,$v).';';
            }
            $userData = substr($userData,0,-1);
        }else{
            $userData = $this->formatParam($config->type,$param[0]);
        }
        //先将中文转码
        $userData = mb_convert_encoding($userData, "GBK", "UTF-8");
        $userData = $DES->encrypt($userData);
        $params = array("userName_" => $partner, "password_" => $partnerPW, "type_" => $type, "param_" => $userData);
        //请求查询
        if(count($param) > 1){//传入的数组有多条数据就查询多个
            $data = $soap->queryBatch($params);
            $resultXML = $DES->decrypt($data->queryBatchReturn);
        }else{
            $data = $soap->querySingle($params);
            $resultXML = $DES->decrypt($data->querySingleReturn);
        }
        //@todo 解密数据
        $resultXML = mb_convert_encoding($resultXML, "UTF-8", "GBK");
        return $resultXML;
    }
    /**
     * 格式化参数
     * @param array $params
     * 参数数组
     * @return string
     */
    function formatParam($queryType, $params) {
        $supportClass = $this->getDI()->getConfig()->validateConfig->supportClass;
        if (empty($supportClass[$queryType])) {
            return -1;
        }
        $keys = array();
        $values = array();
        foreach ($params as $key => $value) {
            $keys[] = $key;
            $values[] = strtoupper($value);
        }
        $param = str_replace($keys, $values, $supportClass[$queryType]);
        return $param;
    }
    /**
     * 取得生日(由身份证号)
     * @param int $id 身份证号
     * @return string
     */
    function getBirthDay($id) {
        switch (strlen($id)) {
            case 15:
                $year = "19" . substr($id, 6, 2);
                $month = substr($id, 8, 2);
                $day = substr($id, 10, 2);
            break;
            case 18:
                $year = substr($id, 6, 4);
                $month = substr($id, 10, 2);
                $day = substr($id, 12, 2);
            break;
        }
        $birthday = array('year' => $year, 'month' => $month, 'day' => $day);
        return $birthday;
    }
    /**
     * 取得性别(由身份证号)--可能不准
     * @param int $id 身份证号
     * @return string
     */
    function getSex($id) {
        switch (strlen($id)) {
            case 15:
                $sexCode = substr($id, 14, 1);
            break;
            case 18:
                $sexCode = substr($id, 16, 1);
            break;
        }
        if ($sexCode % 2) {
            return "男";
        }
        else {
            return "女";
        }
    }
    /**
     * 格式化数据
     * @param string $type
     * @param srring $data
     * @return array
     */
    function formatData($type, $data) {
        switch ($type) {
            case "1A020201":
                $detailInfo = $data['policeCheckInfos']['policeCheckInfo'];
                $birthDay = $this->getBirthDay($detailInfo['identitycard']);
                $sex = $this->getSex($detailInfo['identitycard']);
                $info = array('name' => $detailInfo['name'], 'identitycard' => $detailInfo['identitycard'], 'sex' => $sex, 'compStatus' => $detailInfo['compStatus'], 'compResult' => $detailInfo['compResult'], 'policeadd' => $detailInfo['policeadd'],
                //'checkPhoto' => $detailInfo ['checkPhoto'],
                'birthDay' => $birthDay, 'idcOriCt2' => $detailInfo['idcOriCt2'], 'resultStatus' => $detailInfo['compStatus']);
            break;
            default:
                $info = array(false);
            break;
        }
        return $info;
    }
}
