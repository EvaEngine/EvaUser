<?php

namespace Eva\EvaUser\Controllers\Admin;

use Phalcon\Mvc\Controller;

class ControllerBase extends \Eva\EvaEngine\Mvc\Controller\AdminControllerBase
{
    public function initialize()
    {
        $this->view->setModuleLayout('EvaCommon', '/views/admin/layouts/login');
        $this->view->setModuleViewsDir('EvaUser', '/views');
        $this->view->setModulePartialsDir('EvaCommon', '/views');
    }
}
