<?php

namespace Eva\EvaUser\Controllers\Admin;

class AdminControllerBase extends ControllerBase
{
    public function initialize()
    {
        $this->view->setModuleLayout('EvaCommon', '/views/admin/layouts/layout');
        $this->view->setModuleViewsDir('EvaUser', '/views');
        $this->view->setModulePartialsDir('EvaCommon', '/views');
    }
}
