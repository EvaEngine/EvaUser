<?php

namespace Eva\EvaUser\Controllers\Admin;

use Eva\EvaComment\Entities\Comments;
use Eva\EvaComment\Models\CommentManager;
use Eva\EvaUser\Entities\Users;
use Eva\EvaUser\Models;
use Eva\EvaEngine\Mvc\Controller\JsonControllerInterface;
use Eva\EvaEngine\Exception;
use Eva\EvaEngine\Mvc\Controller\SessionAuthorityControllerInterface;
use Eva\EvaUser\Models\Login as LoginModel;

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
                "conditions" => "username = :q:",
                "limit" => 10,
                "bind" => array(
                    'q' => $query
                )
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
        //避免 Models\UserManager->beforeSave()方法会对password进行再次hash
        $user->password = '';
        if (!$user) {
            return $this->showErrorMessageAsJson(404, 'ERR_USER_NOT_FOUND');
        }

        try {
            $user->status = $this->request->getPut('status');
            $user->save();
        } catch (\Exception $e) {
            return $this->showExceptionAsJson($e, $user->getMessages());
        }

        $userInfo = $this->getUserInfo();
        $operationData = array(
            'operatorId' => $userInfo['id'],
            'subjectUser' => $user,
        );
        $this->getDI()->getEventsManager()->fire('audit:createOperation', $operationData);

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

    /**
     * @operationName("Remove user and comments")
     * @operationDescription("Remove user and comments")
     */
    public function deleteUserCommentAction()
    {
        if (!$this->request->isDelete()) {
            return $this->showErrorMessageAsJson(405, 'ERR_REQUEST_METHOD_NOT_ALLOW');
        }
        $userId = $this->dispatcher->getParam('id');

        //删除评论
        $commentModel = new CommentManager();
        try {
            $comments = $commentModel->findCommentsByUserId($userId);

            foreach ($comments as $comment) {
                $commentModel->updateCommentStatus($comment, Comments::STATE_SPAM);
            }

            $commentModel->syncCommentNum();
        } catch (\Exception $e) {
            return $this->showExceptionAsJson($e, $comment->getMessages());
        }

        //删除用户
        $user =  Models\UserManager::findFirst($userId);
        if (!$user) {
            return $this->showErrorMessageAsJson(404, 'ERR_USER_NOT_FOUND');
        }

        try {
            $user->status = 'deleted';
            $user->save();
        } catch (\Exception $e) {
            return $this->showExceptionAsJson($e, $user->getMessages());
        }

        $userInfo = $this->getUserInfo();
        $operationData = array(
            'operatorId' => $userInfo['id'],
            'subjectUser' => $user,
        );
        $this->getDI()->getEventsManager()->fire('audit:createOperation', $operationData);

        return $this->response->setJsonContent($user);
    }

    private function getUserInfo()
    {
        $user = new LoginModel();
        if ($user->isUserLoggedIn()) {
            $userInfo = $user->getCurrentUser();

            return $userInfo;
        }else{
            return false;
        }
    }
}
