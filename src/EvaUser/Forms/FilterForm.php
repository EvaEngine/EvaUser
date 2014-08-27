<?php
namespace Eva\EvaUser\Forms;

use Eva\EvaEngine\Form;
use Phalcon\Forms\Element\Select;

class FilterForm extends Form
{
    /**
    * @var integer
    */
    public $uid;

    /**
    *
    * @var string
    */
    public $q;

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

    /**
    *
    * @Type(Select)
    * @Option("All Status")
    * @Option(active=Active)
    * @Option(inactive=Inactive)
    * @Option(deleted=Deleted)
    * @var string
    */
    public $status;

    /**
    *
    * @var string
    */
    public $username;

    public function initialize($entity = null, $options = null)
    {
        $this->initializeFormAnnotations();
    }
}
