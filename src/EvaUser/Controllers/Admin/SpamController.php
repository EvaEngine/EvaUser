<?php
namespace Eva\EvaUser\Controllers\Admin;
use Eva\EvaUser\Forms;
use Eva\EvaUser\Models;
use Eva\EvaEngine\Mvc\Controller\SessionAuthorityControllerInterface;

/**
 * @resourceName("Spam User Managment")
 * @resourceDescription("Spam User Managment")
 */
class SpamController extends AdminControllerBase implements SessionAuthorityControllerInterface
{
    /**
     * @operationName("Spam User List")
     * @operationDescription("Spam user list")
     */
    public function indexAction()
    {
        $limit = $this->request->getQuery('limit', 'int', 25);
        $limit = $limit > 100 ? 100 : $limit;
        $limit = $limit < 10 ? 10 : $limit;

        $query = array(
            //'q' => $this->request->getQuery('q', 'string'),
            'status' => $this->request->getQuery('status', 'string', 'spam'),
            'uid' => $this->request->getQuery('uid', 'int'),
            'cid' => $this->request->getQuery('cid', 'int'),
            'username' => $this->request->getQuery('username', 'string'),
            'usernameClearly' => $this->request->getQuery('usernameClearly', 'string'),

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
}
