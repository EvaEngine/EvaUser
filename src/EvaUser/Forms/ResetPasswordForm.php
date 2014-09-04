<?php
namespace Eva\EvaUser\Forms;

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
 * @SWG\Model(id="ResetPasswordForm")
 */
class ResetPasswordForm extends Form
{

    /**
     * @SWG\Property(
     *   name="identify",
     *   type="string",
     *   description="User name or email"
     * )
     */
    public $identify;

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
            )),
            new Confirmation(array(
                'message' => 'Password doesn\'t match confirmation',
                'with' => 'passwordConfirm'
            ))
        ));

        $this->add($password);

        // Confirm Password
        $confirmPassword = new Password('passwordConfirm');

        $confirmPassword->setLabel('Confirm Password');

        $confirmPassword->addValidators(array(
            new PresenceOf(array(
                'message' => 'The confirmation password is required'
            ))
        ));

        $this->add($confirmPassword);
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
