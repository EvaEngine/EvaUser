<?php
namespace Eva\EvaUser\Forms;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Password;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Confirmation;

class ChangePasswordForm extends Form
{

    public function initialize($entity = null, $options = null)
    {
        // Password
        $password = new Password('password');
        $password->setLabel('Old Password');
        $this->add($password);


        // Confirm Password
        $newPassword = new Password('passwordNew');
        $newPassword->setLabel('New Password');
        $newPassword->addValidators(array(
            new PresenceOf(array(
                'message' => 'New password is required'
            )),
            new StringLength(array(
                'min' => 6,
                'messageMinimum' => 'Password is too short. Minimum 6 characters'
            )),
        ));
        $this->add($newPassword);



        // Confirm Password
        $confirmPassword = new Password('passwordConfirm');
        $confirmPassword->setLabel('Confirm Password');
        $confirmPassword->addValidators(array(
            new PresenceOf(array(
                'message' => 'The confirmation password is required'
            )),
            new Confirmation(array(
                'message' => 'Password doesn\'t match confirmation',
                'with' => 'passwordNew'
            ))
        ));
        $this->add($confirmPassword);
    }
}
