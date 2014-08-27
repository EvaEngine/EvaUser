<?php
namespace Eva\EvaUser\Forms;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Email;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email as EmailValidator;

class ChangeEmailForm extends Form
{

    public function initialize($entity = null, $options = null)
    {
        $email = new Email('email');
        $email->setLabel('Change Email');
        $email->addValidators(array(
            new PresenceOf(array(
                'message' => 'The email is required'
            )),
            new EmailValidator(array(
                'message' => 'The email format not correct'
            )),
        ));
        $this->add($email);
    }
}
