<?php
/**
 * Created by PhpStorm.
 * User: zhanghongwen
 * Date: 15/5/22
 * Time: 下午1:13
 */

namespace Eva\EvaUser\Models;

use Eva\EvaUser\Entities\UserAuthLogs;


class UserAuthLog extends UserAuthLogs{

    public function findLogs($query){
        $itemQuery = $this->getDI()->getModelsManager()->createBuilder();

        $itemQuery->from(__CLASS__);

        $orderMapping = array(
            'id' => 'id ASC',
            '-id' => 'id DESC',
            'userId' => 'userId ASC',
            'userId' => 'userId DESC',
            'created_at' => 'createdAt ASC',
            '-created_at' => 'createdAt DESC',
            'username' => 'username ASC',
            '-username' => 'username DESC',
            'requestAt' => 'requestAt ASC',
            '-requestAt' => 'requestAt DESC',
        );
        if (!empty($query['username'])) {
            $itemQuery->andWhere('username LIKE :username:', array('username' => "%{$query['username']}%"));
            // 按匹配度排序
            if (empty($query['order'])) {
                $backendOrder = "REPLACE(username,'{$query['username']}','')";
            }
        }

        if (!empty($query['userId'])) {
            $itemQuery->andWhere('userId = :userId:', array('userId' => $query['userId']));
        }

        if (!empty($query['realName'])) {
            $itemQuery->andWhere('realName LIKE :realName:', array('realName' => "%{$query['realName']}%"));
            if (empty($query['order'])) {
                $backendOrder = "REPLACE(realName,'{$query['realName']}','')";
            }
        }

        if (!empty($query['cardNum'])) {
            $itemQuery->andWhere('cardNum LIKE :cardNum:', array('cardNum' => "%{$query['cardNum']}%"));
        }

        $order = 'id DESC';
        if (!empty($query['order'])) {
            $order = empty($orderMapping[$query['order']]) ? $order : $orderMapping[$query['order']];
            // 后端指定的排序
        } elseif (!empty($backendOrder)) {
            $order = $backendOrder;
        }
        $itemQuery->orderBy($order);

        return $itemQuery;
    }



}