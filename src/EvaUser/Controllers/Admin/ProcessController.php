<?php

namespace Eva\EvaUser\Controllers\Admin;

use Eva\EvaUser\Models;
use Eva\EvaEngine\Mvc\Controller\JsonControllerInterface;
use Eva\EvaEngine\Exception;
use Eva\EvaEngine\Mvc\Controller\SessionAuthorityControllerInterface;

/**
* @resourceName("User Managment Assists")
* @resourceDescription("User Managment Assists (Ajax json format)")
*/
class ProcessController extends ControllerBase implements JsonControllerInterface, SessionAuthorityControllerInterface
{

    /**
    * @operationName("Completed user name")
    * @operationDescription("Completed user name")
    */
    public function suggestionsAction()
    {
        $query = $this->request->get('query');
        if ($query) {
            $users = Models\UserManager::find(array(
                "columns" => array('id', 'username', 'status'),
                "conditions" => "username = '$query'",
                "limit" => 10,
            ));
            $users = $users ? $users->toArray() : array();
        } else {
            $users = array();
        }

        return $this->response->setJsonContent($users);
    }

    /**
    * @operationName("Change user status")
    * @operationDescription("Change user status")
    */
    public function statusAction()
    {
        if (!$this->request->isPut()) {
            throw new Exception\ResourceNotFoundException('ERR_USER_REQUEST_USER_NOT_FOUND');
        }

        $id = $this->dispatcher->getParam('id');
        $user =  Models\UserManager::findFirst($id);
        if (!$user) {
            return $this->showErrorMessageAsJson(404, 'ERR_USER_NOT_FOUND');
        }

        try {
            $user->status = $this->request->getPut('status');
            $user->save();
        } catch (\Exception $e) {
            return $this->showExceptionAsJson($e, $user->getMessages());
        }

        return $this->response->setJsonContent($user);
    }

    /**
    * @operationName("Remove user")
    * @operationDescription("Remove user")
    */
    public function deleteAction()
    {
        if (!$this->request->isDelete()) {
            return $this->showErrorMessageAsJson(405, 'ERR_REQUEST_METHOD_NOT_ALLOW');
        }

        $id = $this->dispatcher->getParam('id');
        $user =  Models\UserManager::findFirst($id);
        try {
            if ($user) {
                $user->delete();
            }
        } catch (\Exception $e) {
            return $this->showExceptionAsJson($e, $user->getMessages());
        }

        return $this->response->setJsonContent($user);
    }
}
