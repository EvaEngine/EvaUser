<?php
namespace Eva\EvaUser\Forms;

use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Password;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Confirmation;

/**
 * @package
 * @category
 * @subpackage
 *
 * @SWG\Model(id="MobileResetPasswordForm")
 */
class MobileResetPasswordForm extends Form
{

    /**
     * @SWG\Property(
     *   name="password",
     *   type="string",
     *   description="the new password"
     * )
     */
    public $password;
    /**
     * @SWG\Property(
     *   name="captcha",
     *   type="string",
     *   description="captcha"
     * )
     */
    public $captcha;

    public function initialize($entity = null, $options = null)
    {
        // Password
        $password = new Password('password');

        $password->setLabel('Password');

        $password->addValidators(array(
            new PresenceOf(array(
                'message' => 'The password is required'
            )),
            new StringLength(array(
                'min' => 6,
                'messageMinimum' => 'Password is too short. Minimum 6 characters'
            ))
        ));
        $this->add($password);
        // Captcha
        $captcha = new Text('captcha');
        $captcha->setLabel('Captcha');
        $captcha->addValidators(array(
            new PresenceOf(array(
                'message' => 'The captcha is required'
            )),
        ));
        $this->add($captcha);
    }
}
