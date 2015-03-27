<?php
return array(
    'user' => array(
        'allowPendingUserLogin' => false,
        'registerUri' => '/admin/login',
        'registerSuccessRedirectUri' => '/admin/login',
        'registerFailedRedirectUri' => '/admin/login',
        'loginUri' => '/admin',
        'loginSuccessRedirectUri' => '/admin/dashboard',
        'loginFailedRedirectUri' => '/admin/login',
        'activeMailTemplate' => __DIR__ . '/../views/mails/active.phtml',
        'activeSuccessRedirectUri' => '/admin/login',
        'activeFailedRedirectUri' => '/admin/login',
        'resetSuccessRedirectUri' => '/admin/login',
        'resetFailedRedirectUri' => '/admin/login',
        'resetMailTemplate' => __DIR__ . '/../views/mails/reset.phtml',
        'changeEmailTemplate' => __DIR__ . '/../views/mails/change_email.phtml',
        'cookieTokenExpired' => 500000,
        'loginCookieDomain' => '',
    ),
    'validateConfig' => array(
        'wsdlURL' => "http://gboss.id5.cn/services/QueryValidatorServices?wsdl",
        'partner' => "ANXXService",
        'partnerPW' => "ANXXService_V)~6L!s)",
        'Key' => "12345678",
        'iv' => "12345678",
        'type' =>'1A020201',
        'supportClass' => array ("1A020201" => "Name,CardNum" )
    )
);
