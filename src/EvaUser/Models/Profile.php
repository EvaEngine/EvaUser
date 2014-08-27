<?php

namespace Eva\EvaUser\Models;

use Eva\EvaUser\Entities;

class Profile extends Entities\Profiles
{
    public function beforeSave()
    {
        if (!$this->birthday) {
            $this->skipAttributes(array('birthday'));
        }
    }
}
