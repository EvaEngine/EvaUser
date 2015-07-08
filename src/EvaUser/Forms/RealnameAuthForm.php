<?php
namespace Eva\EvaUser\Forms;

use Eva\EvaEngine\Form;

/**
 * @package
 * @category
 * @subpackage
 *
 * @SWG\Model(id="RealnameAuthForm")
 */
class RealnameAuthForm extends Form
{
    /**
     * @SWG\Property(
     *   name="realName",
     *   type="string",
     *   description="realName"
     * )
     */
    public $realName;

    /**
     * @SWG\Property(
     *   name="cardNum",
     *   type="string",
     *   description="idCard Num"
     * )
     */
    public $cardNum;

}
