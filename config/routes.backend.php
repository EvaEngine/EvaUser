<?php

return array(
    '/admin/user' =>  array(
        'module' => 'EvaUser',
        'controller' => 'Admin\User',
    ),

    '/admin/user/:action(/(\d+))*' =>  array(
        'module' => 'EvaUser',
        'controller' => 'Admin\User',
        'action' => 1,
        'id' => 3,
    ),

    '/admin/user/process/:action(/(\d+))*' =>  array(
        'module' => 'EvaUser',
        'controller' => 'Admin\Process',
        'action' => 1,
        'id' => 3,
    ),

    '/admin/user/deleteusercomment/(\d+)' =>  array(
        'module' => 'EvaUser',
        'controller' => 'Admin\Process',
        'action' => 'deleteUserComment',
        'id' => 1,
    ),

    '/admin/register' => array(
        'module' => 'EvaUser',
        'controller' => 'Admin\Register',
    ),

    '/admin/register/:action' => array(
        'module' => 'EvaUser',
        'controller' => 'Admin\Register',
        'action' => 1,
    ),

    '/admin/login' => array(
        'module' => 'EvaUser',
        'controller' => 'Admin\Login',
    ),

    '/admin/login/:action([\w/]*)' => array(
        'module' => 'EvaUser',
        'controller' => 'Admin\Login',
        'action' => 1,
    ),

    '/admin/logout' => array(
        'module' => 'EvaUser',
        'controller' => 'Admin\Logout',
    ),

    '/admin/session/:action' => array(
        'module' => 'EvaUser',
        'controller' => 'Admin\Session',
        'action' => 1,
    ),

    '/admin/session/verify/(\w+)/(\w+)' => array(
        'module' => 'EvaUser',
        'controller' => 'Admin\Session',
        'action' => 'verify',
        'username' => 1,
        'code' => 2,
    ),

    '/admin/session/reset/(\w+)/(\w+)' => array(
        'module' => 'EvaUser',
        'controller' => 'Admin\Session',
        'action' => 'reset',
        'username' => 1,
        'code' => 2,
    ),
    '/admin/spam-user' => array(
        'module' => 'EvaUser',
        'controller' => 'Admin\Spam',
        'action' => 'index'
    ),
    '/admin/login-history' =>  array(
        'module' => 'EvaUser',
        'controller' => 'Admin\User',
        'action' => 'loginHistory'
    ),
   '/cleancookies' =>  array(
        'module' => 'EvaUser',
        'controller' => 'logout',
        'action' => 'cleancookies',
    ),
);
