<?php
namespace Eva\EvaUser\Forms;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Password;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;

/**
 * @package
 * @category
 * @subpackage
 *
 * @SWG\Model(id="LoginForm")
 */
class LoginForm extends Form
{
    /**
     * @SWG\Property(
     *   name="identify",
     *   type="string",
     *   description="User name or email"
     * )
     */
    public $identify;

    /**
     * @SWG\Property(name="password",type="string",description="Password of the user")
     */
    public $password;

    public function initialize($entity = null, $options = null)
    {
        $name = new Text('identify');
        $name->addValidators(array(
            new PresenceOf(array(
                'message' => 'Please input username or email'
            ))
        ));
        $this->add($name);

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
    }
}
