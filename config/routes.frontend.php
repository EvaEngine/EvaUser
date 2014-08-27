<?php

return array(
    '/session/verify/(\w+)/(\w+)' => array(
        'module' => 'EvaUser',
        'controller' => 'Session',
        'action' => 'verify',
        'username' => 1,
        'code' => 2,
    ),

    '/session/reset/(\w+)/(\w+)' => array(
        'module' => 'EvaUser',
        'controller' => 'Session',
        'action' => 'reset',
        'username' => 1,
        'code' => 2,
    ),
);
