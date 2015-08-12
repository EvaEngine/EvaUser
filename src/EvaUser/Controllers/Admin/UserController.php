<?php

namespace Eva\EvaUser\Controllers\Admin;

use Eva\EvaUser\Forms;
use Eva\EvaUser\Models;
use Eva\EvaEngine\Mvc\Controller\SessionAuthorityControllerInterface;

/**
 * @resourceName("User Managment")
 * @resourceDescription("User Managment")
 */
class UserController extends AdminControllerBase implements SessionAuthorityControllerInterface
{

    /**
     * @operationName("User List")
     * @operationDescription("Get user list")
     */
    public function indexAction()
    {
        $limit = $this->request->getQuery('per_page', 'int', 25);
        $limit = $limit > 100 ? 100 : $limit;
        $limit = $limit < 10 ? 10 : $limit;
        $query = array(
            //'q' => $this->request->getQuery('q', 'string'),
            'status' => $this->request->getQuery('status', 'string'),
            'uid' => $this->request->getQuery('uid', 'int'),
            'cid' => $this->request->getQuery('cid', 'int'),
            'username' => $this->request->getQuery('username', 'string'),
            'usernameClearly' => $this->request->getQuery('usernameClearly', 'string'),
            'source' => $this->request->getQuery('source', 'string'),

            'email' => $this->request->getQuery('email', 'string'),
            'mobile' => $this->request->getQuery('mobile', 'string'),
            'screenName' => $this->request->getQuery('screenName', 'string'),

            'order' => $this->request->getQuery('order', 'string'),
            'limit' => $limit,
            'page' => $this->request->getQuery('page', 'int', 1),
        );

        $form = new Forms\FilterForm();
        $form->setValues($this->request->getQuery());
        $this->view->setVar('form', $form);
        $user = new Models\UserManager();
        $users = $user->findUsers($query);
        $paginator = new \Eva\EvaEngine\Paginator(array(
            "builder" => $users,
            "limit" => $limit,
            "page" => $query['page']
        ));
        $paginator->setQuery($query);
        $pager = $paginator->getPaginate();
        $this->view->setVar('pager', $pager);
    }

    /**
     * @operationName("Create User")
     * @operationDescription("Create User")
     */
    public function createAction()
    {
        $user = new Models\UserManager();
        $form = new \Eva\EvaUser\Forms\UserForm();
        $form->setModel($user);
        $form->addForm('profile', 'Eva\EvaUser\Forms\ProfileForm');
        $this->view->setVar('item', $user);
        $this->view->setVar('form', $form);

        if (!$this->request->isPost()) {
            return false;
        }

        $data = $this->request->getPost();
        if (!$form->isFullValid($data)) {
            return $this->showInvalidMessages($form);
        }

        try {
            $form->save();
        } catch (\Exception $e) {
            return $this->showException($e, $form->getModel()->getMessages());
        }
        $this->flashSession->success('SUCCESS_USER_CREATED');

        return $this->redirectHandler('/admin/user/edit/' . $form->getModel()->id);
    }

    /**
     * @operationName("Edit User")
     * @operationDescription("Edit User")
     */
    public function editAction()
    {
        $this->view->changeRender('admin/user/create');
        $user = Models\UserManager::findFirst($this->dispatcher->getParam('id'));
        if (!$user) {
        }

        $form = new \Eva\EvaUser\Forms\UserForm();
        $form->setModel($user);
        $form->addForm('profile', 'Eva\EvaUser\Forms\ProfileForm');
        $this->view->setVar('item', $user);
        $this->view->setVar('form', $form);

        if (!$this->request->isPost()) {
            return false;
        }

        $data = $this->request->getPost();
        if (!$form->isFullValid($data)) {
            return $this->showInvalidMessages($form);
        }

        try {
            $form->save();
        } catch (\Exception $e) {
            return $this->showException($e, $form->getModel()->getMessages());
        }
        $this->flashSession->success('SUCCESS_USER_UPDATED');

        return $this->redirectHandler('/admin/user/edit/' . $user->id);

    }

    /**
     * @operationName("auth log List")
     * @operationDescription("auth log List")
     */
    public function authLogAction()
    {
        $limit = $this->request->getQuery('limit', 'int', 25);
        $limit = $limit > 100 ? 100 : $limit;
        $limit = $limit < 10 ? 10 : $limit;
        $query = array(
            //'q' => $this->request->getQuery('q', 'string'),
            'userId' => $this->request->getQuery('userId', 'int'),
            'username' => $this->request->getQuery('username', 'string'),
            'realName' => $this->request->getQuery('realName', 'string'),
            'cardNum' => $this->request->getQuery('cardNum', 'string'),
            'order' => $this->request->getQuery('order', 'string'),
            'limit' => $limit,
            'page' => $this->request->getQuery('page', 'int', 1),
        );
        $form = new Forms\AuthLogForm();
        $form->setValues($this->request->getQuery());

        $this->view->setVar('form', $form);
        $logModel = new Models\UserAuthLog();
        $logs = $logModel->findlogs($query);


        $paginator = new \Eva\EvaEngine\Paginator(array(
            "builder" => $logs,
            "limit" => $limit,
            "page" => $query['page']
        ));
        $paginator->setQuery($query);
        $pager = $paginator->getPaginate();
        $this->view->setVar('pager', $pager);
    }

    public function loginHistoryAction()
    {
        $limit = $this->request->getQuery('limit', 'int', 25);
        $limit = $limit > 100 ? 100 : $limit;
        $limit = $limit < 10 ? 10 : $limit;
        $query = array(
            'order' => $this->request->getQuery('order', 'string', '-created_at'),
            'limit' => $limit,
            'page' => $this->request->getQuery('page', 'int', 1),
        );
        $user = new Models\UserManager();
        $users = $user->findLoginedUsers($query);
        $paginator = new \Eva\EvaEngine\Paginator(array(
            "builder" => $users,
            "limit" => $limit,
            "page" => $query['page']
        ));
        $paginator->setQuery($query);
        $pager = $paginator->getPaginate();
        $this->view->setVar('pager', $pager);
    }

}
