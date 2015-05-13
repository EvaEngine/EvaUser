<?php

namespace Eva\EvaUser\Entities;

class WeiboUsers extends EvaUserEntityBase implements CommentUser
{
    protected $tableName = 'weibo_users';
    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $screenName;

    /**
     *
     * @var string
     */
    public $avatar;

    const USER_TYPE='weibo';


    public $cachePrefix = 'eva_user_weibo_users_';

    public $cacheTime = 86400;  //一天

    public function getCache()
    {
        /** @var \Phalcon\Cache\Backend\Libmemcached $cache */
        $cache =  $this->getDI()->get('modelsCache');
        return $cache;
    }

    public function createCacheKey($params){
        ksort($params);
        $str = $this->cachePrefix;
        foreach($params as $k=>$v){
            $str .= $k.'_'.$v.'_';
        }

        return $str;
    }

    public function refreshCache($params)
    {
        $cacheKey = $this->createCacheKey($params);
        if($this->getCache()->exists($cacheKey)){
            $this->getCache()->delete($cacheKey);
        }
    }


    public function afterSave()
    {
//        $this->refreshCache(array('userId'=>$this->userId));
//        $this->refreshCache(array('postId'=>$this->userId));
//        $this->refreshCache(array('userId'=>$this->userId,'postId'=>$this->postId));
    }

    public function afterDelete()
    {
//        $this->refreshCache(array('userId'=>$this->userId));
//        $this->refreshCache(array('postId'=>$this->userId));
//        $this->refreshCache(array('userId'=>$this->userId,'postId'=>$this->postId));
    }

    public function getId()
    {
        return $this->avatar;
    }

    public function getName()
    {
        return $this->screenName ?: $this->name;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }

    public function getUserType()
    {
        return self::USER_TYPE;
    }

}
