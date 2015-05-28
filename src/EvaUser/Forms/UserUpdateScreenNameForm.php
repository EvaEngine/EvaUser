<?php

namespace Eva\EvaUser\Forms;

use Eva\EvaEngine\Form;
use Eva\EvaEngine\Exception;
use Eva\EvaUser\Models\Login;
use Eva\EvaUser\Models\User;
use Phalcon\Forms\Element\Password;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Confirmation;
use Phalcon\Validation;

/**
 * @SWG\Model(id="UserUpdateScreenName")
 */
class UserUpdateScreenNameForm extends Form
{
    /**
     * @SWG\Property
     * required=true
     * @var
     */
    public $screenName;




}