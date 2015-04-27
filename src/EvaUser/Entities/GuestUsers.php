<?php

namespace Eva\EvaUser\Entities;

class GuestUsers extends EvaUserEntityBase implements CommentUser
{
    const USER_TYPE = 'guest';

    public function getId()
    {
        return 0;
    }

    public function getName()
    {
        return '';
    }

    public function getAvatar()
    {
        return '';
    }

    public function getUserType()
    {
        return self::USER_TYPE;
    }

}
