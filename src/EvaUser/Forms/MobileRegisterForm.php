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
 * @SWG\Model(id="MobileRegisterForm")
 */
class MobileRegisterForm extends Form
{
    /**
     * @SWG\Property(name="username",type="string",description="Username, allow alphanumeric and underline")
     */
    public $username;

    /**
     * @SWG\Property(name="mobile",type="string",description="Mobile number")
     */
    public $mobile;

    /**
     * @SWG\Property(name="password",type="string",description="Password of the user")
     */
    public $password;

    /**
     * @SWG\Property(name="captcha",type="string",description="Mobile captcha")
     */
    public $captcha;

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

        $mobile = new Text('mobile');
        $mobile->setLabel('Mobile');
        $mobile->addValidators(array(
            new PresenceOf(array(
                'message' => 'Mobile is required'
            )),
            new Regex(array(
                'pattern' => '/^\d+$/',
                'message' => 'Mobile is number only'
            )),
        ));
        $this->add($mobile);

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


        // Captcha
        $captcha = new Text('captcha');
        $captcha->setLabel('Captcha');
        $captcha->addValidators(array(
            new PresenceOf(array(
                'message' => 'The captcha is required'
            )),
        ));
        $this->add($captcha);

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
