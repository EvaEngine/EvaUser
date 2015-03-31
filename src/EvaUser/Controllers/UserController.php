<?php
/**
 * Created by PhpStorm.
 * User: wscn
 * Date: 15/3/30
 * Time: 下午6:26
 */

namespace Eva\EvaUser\Controllers;


use Eva\EvaEngine\Mvc\Controller\SessionAuthorityControllerInterface;
use Eva\EvaUser\Forms\MobileBindingForm;
use Eva\EvaUser\Models\Login;
use Eva\EvaUser\Models\User;
use Eva\EvaEngine\Exception;


class UserController extends ControllerBase implements SessionAuthorityControllerInterface
{
    /**
     * @operationName("users mobile binding")
     * @operationDescription("users mobile binding")
     */
    public function bindMobileAction()
    {
        $bindingForm = new MobileBindingForm();
        $curUser = Login::getCurrentUser();
        $bindingForm->userId = $curUser['id'];
        $bindingForm->captcha = $this->request->getPut('captcha');
        $bindingForm->mobile = $this->request->getPut('mobile');
        try {
            if (!User::bindMobile($bindingForm)) {
                return $this->showErrorMessageAsJson(400, 'BIND_MOBILE_FAILURE');
            }
        } catch (Exception\LogicException $e) {
            return $this->showExceptionAsJson($e);
        }
        return $this->showResponseAsJson(['mobile' => $bindingForm->mobile, 'status' => true]);
    }
}