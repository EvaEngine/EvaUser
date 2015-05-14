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
 * @SWG\Model(id="UserUpdatePassword")
 */
class UserUpdatePasswordForm extends Form
{
    /**
     * @SWG\Property
     * required=true
     * @var
     */
    public $oldPassword;
    /**
     * @SWG\Property
     * required=true
     * @var
     */
    public $password;


    public function initialize()
    {
        $password = new Password('password');

        $password->addValidators([
            new PresenceOf([
                'message' => 'A new password is required.'
            ]),
            new StringLength([
                'min' => 6,
                'messageMinimum' => 'The new password is too short. Minimum 6 characters.'
            ]),
            new NotSameValidator([
                'message' => 'The new password and the old are the same.',
                'with' => 'oldPassword'
            ])
        ]);

        $this->add($password);

        $oldPassword = new Password('oldPassword');
        $oldPassword->addValidators([
            new PresenceOf([
                'message' => 'The old password is required.'
            ]),
            new OldPasswordValidator(),
        ]);

        $this->add($oldPassword);
    }

}

class OldPasswordValidator extends Validation\Validator implements Validation\ValidatorInterface
{

    /**
     * @param $validator
     * @param string $attribute
     * @return bool
     */
    public function validate($validator, $attribute)
    {
        $value = $validator->getValue($attribute);

        $usr = Login::getCurrentUser();
        if (!$usr['id']) {
            $validator->appendMessage(new Validation\Message('ERR_USER_NOT_LOGIN', $attribute));

            return false;
        }
        /**
         * @var $usr User
         */
        $usr = User::findFirst('id = ' . $usr['id']);
        if (!Login::passwordVerify($value, $usr->password)) {

            $message = $this->getOption('message');
            if (!$message) {
                $message = 'The old password provided is incorrect.';
            }

            $validator->appendMessage(new Validation\Message($message, $attribute));

            return false;
        }

        return true;
    }

}


class NotSameValidator extends Validation\Validator implements Validation\ValidatorInterface
{
    public function validate($validator, $attribute)
    {

        $with = $validator->getValue($this->getOption('with'));
        $value = $validator->getValue($attribute);
        if ($with === $value) {
            $message = $this->getOption('message');
            $validator->appendMessage(new Validation\Message($message, $attribute));

            return false;
        }

        return true;
    }


}
