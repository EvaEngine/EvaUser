<?php

namespace Eva\EvaUser\Models;

use Eva\EvaEngine\IoC;
use Eva\EvaEngine\View\PurePaginator;
use Eva\EvaUser\Entities;
use Eva\EvaFileSystem\Models\Upload as UploadModel;
use Eva\EvaEngine\Exception;
use Phalcon\Mvc\Model\Query;

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
        $this->updatedAt = time();
        $this->providerType = $this->providerType != 'DEFAULT' ? $this->providerType : 'ADMIN';
    }

    public function beforeUpdate()
    {
        $this->updatedAt = time();
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
            $file = $upload->upload($file, 'userAvatar');
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
            'last_login' => 'loginAt ASC',
            '-last_login' => 'loginAt DESC',
        );
        if (!empty($query['username'])) {
            $itemQuery->andWhere('username LIKE :username:', array('username' => "%{$query['username']}%"));
            // 按匹配度排序
            if (empty($query['order'])) {
                $backendOrder = "REPLACE(username,'{$query['username']}','')";
            }
        }
        if (!empty($query['usernameClearly'])) {
            $itemQuery->andWhere('username = :username:', array('username' => $query['usernameClearly']));
        }
        if (!empty($query['email'])) {
            $itemQuery->andWhere('email = :email:', array('email' => trim($query['email'])));
        }
        if (!empty($query['mobile'])) {
            $itemQuery->andWhere('mobile = :mobile:', array('mobile' => trim($query['mobile'])));
        }
        if (!empty($query['screenName'])) {
            $screenName = trim($query['screenName']);
            $itemQuery->andWhere('screenName LIKE :screenName:', array('screenName' => "%{$screenName}%"));
            // 按匹配度排序
            if (empty($query['order'])) {
                $backendOrder = "REPLACE(screenName,'{$screenName}','')";
            }
        }
        if (!empty($query['status'])) {
            $itemQuery->andWhere('status = :status:', array('status' => $query['status']));
        }

        if (!empty($query['source'])) {
            $itemQuery->andWhere('source = :source:', array('source' => $query['source']));
        }

        if (!empty($query['uid'])) {
            $itemQuery->andWhere('id = :uid:', array('uid' => $query['uid']));
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

    public function updateSpamUser($id, $reason)
    {
        if ($id > 0) {
            /** @var Entities\users $user */
            $user = Entities\Users::findFirst($id);
            $user->status = 'spam';
            $user->spamReason = $reason;
            $user->updatedAt = time();
            if ($user->save() === false) {
                throw new Exception\RuntimeException('Save user failed');
            }
        }
    }

    public function findLoginedUsers($query)
    {
        $itemQuery = $this->getDI()->getModelsManager()->createBuilder();

        $itemQuery->from(['U' => __CLASS__]);

        $orderMapping = array(
            'id' => 'id ASC',
            '-id' => 'id DESC',
            'created_at' => 'createdAt ASC',
            '-created_at' => 'createdAt DESC',
            'username' => 'username ASC',
            '-username' => 'username DESC',
            'last_login' => 'loginAt ASC',
            '-last_login' => 'loginAt DESC',
        );

        $itemQuery->join('Eva\EvaUser\Entities\LoginRecords', 'U.id = L.userId', 'L');

        $itemQuery->andWhere("L.source = 'xgb'");

        if (!empty($query['source'])) {
            $itemQuery->andWhere('U.source = :source:', array('source' => $query['source']));
        }

        $itemQuery->groupBy(array('L.userId'));

        $order = 'id DESC';
        if (!empty($query['order'])) {
            $order = empty($orderMapping[$query['order']]) ? $order : $orderMapping[$query['order']];
        }

        $itemQuery->orderBy($order);

        return $itemQuery;
    }

    /**
     * @param \stdClass $pager
     * @param Query\Builder $builder
     * @param integer $limit
     * @return \stdClass
     *
     * 因为Phalcon的分页，在连表查询时候，会产生bug，导致分页出错，所以用此方法来对其重新赋值
     */
    public function correctPaginator(\stdClass $pager, \Phalcon\Mvc\Model\Query\Builder $builder, $limit)
    {
        $builderArray = $builder->getQuery()->execute()->toArray();
        $newPaginator = new PurePaginator($limit, count($builderArray), $builderArray);
        $pager->before = $newPaginator->before;
        $pager->first = $newPaginator->first;
        $pager->next = $newPaginator->next;
        $pager->last = $newPaginator->last;
        $pager->current = $newPaginator->current;
        $pager->total_items = $newPaginator->total_items;
        $pager->total_pages = $newPaginator->total_pages;
        $pager->page_range = $newPaginator->page_range;
        $pager->next_range = $newPaginator->next_range;
        $pager->prev_range = $newPaginator->prev_range;
        return $pager;
    }
}
