<?php

namespace Eva\EvaUser\Models;

use Eva\EvaUser\Entities;
use Eva\EvaFileSystem\Models\Upload as UploadModel;
use Eva\EvaEngine\Exception;

class UserManager extends User
{
    public static $defaultDump = array(
        'id',
        'username',
        'email',
        'status',
        'screenName',
        'firstName',
        'lastName',
        'gender',
        'avatar',
        'emailStatus',
        'createdAt',
        'loginAt',
        'providerType',
    );

    public function beforeCreate()
    {
        $this->createdAt = $this->createdAt ? $this->createdAt : time();
        $this->providerType = $this->providerType != 'DEFAULT' ? $this->providerType : 'ADMIN';
    }

    public function beforeUpdate()
    {
        if (!$this->password) {
            $this->skipAttributesOnUpdate(array('password'));
        }
    }

    public function beforeSave()
    {
        if ($this->password) {
            $this->password = self::passwordHash($this->password);
        }

        if ($this->getDI()->getRequest()->hasFiles()) {
            $upload = new UploadModel();
            $files = $this->getDI()->getRequest()->getUploadedFiles();
            if (!$files) {
                return;
            }
            $file = $files[0];
            $file = $upload->upload($file);
            if ($file) {
                $this->avatarId = $file->id;
                $this->avatar = $file->getFullUrl();
            }
        }
    }

    public function isExist()
    {
        $userinfo = array();
        if ($this->id) {
            $userinfo = self::findFirst("id = '$this->id'");
        } elseif ($this->username) {
            $userinfo = self::findFirst("username = '$this->username'");
        } elseif ($this->email) {
            $userinfo = self::findFirst("email = '$this->email'");
        }

        return $userinfo ? $userinfo->id : false;
    }

    public function findUsers(array $query = array())
    {
        $itemQuery = $this->getDI()->getModelsManager()->createBuilder();

        $itemQuery->from(__CLASS__);

        $orderMapping = array(
            'id' => 'id ASC',
            '-id' => 'id DESC',
            'created_at' => 'createdAt ASC',
            '-created_at' => 'createdAt DESC',
            'username' => 'username ASC',
            '-username' => 'username DESC',
        );

        if (!empty($query['username'])) {
            $itemQuery->andWhere('username LIKE :username:', array('username' => "%{$query['username']}%"));
        }
        if (!empty($query['email'])) {
            $itemQuery->andWhere('email = :email:', array('email' => trim($query['email'])));
        }
        if (!empty($query['status'])) {
            $itemQuery->andWhere('status = :status:', array('status' => $query['status']));
        }

        if (!empty($query['uid'])) {
            $itemQuery->andWhere('id = :uid:', array('uid' => $query['uid']));
        }

        $order = 'id DESC';
        if (!empty($query['order'])) {
            $order = empty($orderMapping[$query['order']]) ? $order : $orderMapping[$query['order']];
        }
        $itemQuery->orderBy($order);

        return $itemQuery;
    }

    public function createUser($data)
    {
        $profileData = empty($data['profile']) ? array() : $data['profile'];
        $profile = new Profile();
        $profile->assign($profileData);
        $this->profile = $profile;

        $this->assign($data);
        if (!$this->save()) {
            throw new Exception\RuntimeException('Create user failed');
        }

        return $this;
    }

    public function updateUser($data)
    {
        $profileData = empty($data['profile']) ? array() : $data['profile'];
        $profile = new Profile();
        $profile->assign($profileData);
        $this->profile = $profile;

        $this->assign($data);
        if (!$this->save()) {
            throw new Exception\RuntimeException('Create user failed');
        }

        return $this;
    }

    public function removeUser($id)
    {
        $this->id = $id;
        $this->delete();
    }
}
