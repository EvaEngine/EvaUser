<?php

namespace Eva\EvaUser\Entities;

class Tokens extends EvaUserEntityBase
{
    /**
     *
     * @var string
     */
    public $sessionId;

    /**
     *
     * @var string
     */
    public $token;

    /**
     *
     * @var string
     */
    public $userHash;

    /**
     *
     * @var integer
     */
    public $userId;

    /**
     *
     * @var integer
     */
    public $refreshAt;

    /**
     *
     * @var integer
     */
    public $expiredAt;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'sessionId' => 'sessionId',
            'token' => 'token',
            'userHash' => 'userHash',
            'userId' => 'userId',
            'refreshAt' => 'refreshAt',
            'expiredAt' => 'expiredAt'
        );
    }

    protected $tableName = 'user_tokens';
}
