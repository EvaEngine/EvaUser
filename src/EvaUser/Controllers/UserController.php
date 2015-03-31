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

/**
 * @resourceName("Users frontend")
 * @resourceDescription("Users frontend")
 */
class UserController extends ControllerBase implements SessionAuthorityControllerInterface
{
    /**
     * @operationName("users mobile binding")
     * @operationDescription("users mobile binding")
     */
    public function bindMobileAction()
    {
        $bindingForm = new MobileBindingForm(new User());
        $curUser = Login::getCurrentUser();
        $data = $this->request->getPut();
        $data['userId'] = $curUser['id'];
        if (!$bindingForm->isValid($data)) {
            return $this->showInvalidMessagesAsJson($bindingForm);
        }
        try {
            if (!User::bindMobile($data['mobile'], $data['captcha'], $data['userId'])) {
                return $this->showErrorMessageAsJson(400, 'BIND_MOBILE_FAILURE');
            }
        } catch (Exception\LogicException $e) {
            return $this->showExceptionAsJson($e);
        }
        return $this->showResponseAsJson(['mobile' => $data['mobile'], 'status' => true]);
    }
}