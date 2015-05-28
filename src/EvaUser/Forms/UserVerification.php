<?php

namespace Eva\EvaUser\Forms;

use \Eva\EvaEngine\Mvc\Model;
use Eva\EvaEngine\Exception;

/**
 * @SWG\Model(id="UserVerification")
 */
class UserVerification extends Form
{
    /**
     * @SWG\Property(
     * description="a mobile number or an email address"),
     * required=true
     * @var
     */
    public $identifier;
    /**
     * @SWG\Property(
     * enum="['EMAIL', 'MOBILE']"),
     * required=true
     * @var
     */
    public $identifierType;

    /**
     * @SWG\Property(
     * name="role",type="string",
     * enum="['USR_REGISTER', 'USR_EDIT']",
     * description="The purpose of this verification. Useful when customising verification templates at different stages."),
     * required=true
     * @var
     */
    public $role;


}
