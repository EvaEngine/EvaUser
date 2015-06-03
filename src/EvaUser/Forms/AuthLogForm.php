<?php
namespace Eva\EvaUser\Forms;

use Eva\EvaEngine\Form;
use Phalcon\Forms\Element\Select;

class AuthLogForm extends Form
{
    /**
    * @var integer
    */
    public $id;

    /**
    *
    * @var integer
    */
    public $userId;

    /**
     *
     * @var string
     */
    public $username;

    /**
     *
     * @var string
     */
    public $realName;

    /**
     *
     * @var string
     */
    public $cardNum;

    /**
     *
     * @var integer
     */
    public $requestAt;

    /**
     *
     * @var integer
     */
    public $quantity;

    /**
     *
     * @var string
     */
    public $status;

    /**
     *
     * @var string
     */
    public $compStatus;

    /**
     *
     * @var string
     */
    public $message;

    /**
     *
     * @var string
     */
    public $compMessage;

    /**
     *
     * @Type(Select)
     * @Option("25":"25")
     * @Option("10":"10")
     * @Option("50":"50")
     * @Option("100":"100")
     * @var string
     */
    public $limit;

    public function initialize($entity = null, $options = null)
    {
        $this->initializeFormAnnotations();
    }
}
