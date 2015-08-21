<?php
/**
 * Wscn
 *
 * @link: https://github.com/wallstreetcn/wallstreetcn
 * @author: franktung<franktung@gmail.com>
 * @Date: 15/8/3
 * @Time: 上午11:13
 *
 */

namespace Eva\EvaUser\Entities;


class LoginSources extends EvaUserEntityBase
{
    /**
     * @var integer
     */
    public $id;
    /**
     * @var integer
     */
    public $userId;

    /**
     * @var string
     */
    public $source;

    /**
     * @var integer
     */
    public $loginAt;

    /**
     * @var integer
     */
    public $remoteIp;

    protected $tableName = 'user_login_sources';
} 