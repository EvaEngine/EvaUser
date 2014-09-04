<?php
namespace Eva\EvaUser\Forms;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Submit;
use Phalcon\Forms\Element\Check;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\Identical;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Confirmation;
use Phalcon\Validation\Validator\Regex;

/**
 * @package
 * @category
 * @subpackage
 *
 * @SWG\Model(id="RegisterForm")
 */
class RegisterForm extends Form
{
    /**
    * @SWG\Property(name="username",type="string",description="Username, allow alphanumeric and underline")
     */
    public $username;

    /**
     * @SWG\Property(name="email",type="string",description="Email")
     */
    public $email;

    /**
     * @SWG\Property(name="password",type="string",description="Password of the user")
     */
    public $password;

    public function initialize($entity = null, $options = null)
    {
        $name = new Text('username');
        $name->setLabel('Name');
        $name->addValidators(array(
            new PresenceOf(array(
                'message' => 'Username is required'
            )),
            new Regex(array(
                'pattern' => '/[0-9a-zA-Z_]+/',
                'message' => 'Username is alphanumerics and underline only'
            )),
            new StringLength(array(
                'min' => 4,
                'max' => 24,
                'messageMinimum' => 'Username is too short. Minimum 4 characters',
                'messageMaximum' => 'Username is too long. Maximum 24 characters'
            )),
        ));
        $this->add($name);

        // Email
        $email = new Text('email');
        $email->setLabel('Email');
        $email->addValidators(array(
            new PresenceOf(array(
                'message' => 'Email is required'
            )),
            new Email(array(
                'message' => 'Email is not valid'
            ))
        ));
        $this->add($email);

        // Password
        $password = new Password('password');
        $password->setLabel('Password');
        $password->addValidators(array(
            new PresenceOf(array(
                'message' => 'The password is required'
            )),
            new StringLength(array(
                'min' => 6,
                'max' => 26,
                'messageMinimum' => 'Password is too short. Minimum 6 characters',
                'messageMaximum' => 'Password is too long. Maximum 26 characters'
            )),
        ));
        $this->add($password);

        // Confirm Password
        /*
        $confirmPassword = new Password('passwordConfirm');
        $confirmPassword->setLabel('Confirm Password');
        $confirmPassword->addValidators(array(
            new PresenceOf(array(
                'message' => 'The confirmation password is required'
            )),
            new Confirmation(array(
                'message' => 'Password doesn\'t match',
                'with' => 'password'
            ))
        ));
        $this->add($confirmPassword);
        */

        // Remember
        $terms = new Check('agree', array(
            'value' => 'yes'
        ));
        $terms->setLabel('Accept terms and conditions');
        $terms->addValidator(new Identical(array(
            'value' => 'yes',
            'message' => 'Terms and conditions must be accepted'
        )));
        $this->add($terms);

        /*
        // CSRF
        $csrf = new Hidden('csrf');

        $csrf->addValidator(new Identical(array(
            'value' => $this->security->getSessionToken(),
            'message' => 'CSRF validation failed'
        )));

        $this->add($csrf);

        // Sign Up
        $this->add(new Submit('Sign Up', array(
            'class' => 'btn btn-success'
        )));
        */
    }

    /**
     * Prints messages for a specific element
     */
    public function messages($name)
    {
        if ($this->hasMessagesFor($name)) {
            foreach ($this->getMessagesFor($name) as $message) {
                $this->flash->error($message);
            }
        }
    }
}
