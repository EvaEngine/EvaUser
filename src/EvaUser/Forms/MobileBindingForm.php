<?php
namespace Eva\EvaUser\Forms;

use Eva\EvaEngine\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Submit;
use Phalcon\Forms\Element\Check;
use Phalcon\Validation\Validator\Between;
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
 * @SWG\Model(id="MobileBindingForm")
 */
class MobileBindingForm extends Form
{
    /**
     * @SWG\Property(name="mobile",type="string",description="Mobile number")
     */
    public $mobile;

    /**
     * @SWG\Property(name="captcha",type="string",description="Mobile captcha")
     */
    public $captcha;


    public $userId;

    public function initialize($entity = null, $options = null)
    {
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
        // Captcha
        $captcha = new Text('userId');
        $captcha->setLabel('User ID');
        $captcha->addValidators(array(
            new PresenceOf(array(
                'message' => 'The userId is required'
            )),
            new Between(
                array(
                    'minimum' => 1,
                    'maximum' => PHP_INT_MAX,
                    'message' => 'The user id must be larger than 1'
                )
            )
        ));
        $this->add($captcha);

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
