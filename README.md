EvaUser - Stardard user module of EvaEngine
=======

### 基础登录流程

用户登录同时支持Session/Token两种形式的登录

#### Session登录流程：

1. 触发事件`user:beforeLogin`
2. 检查数据库中是否存在用户
3. 将`failedLogins`字段清零
4. 更新登录时间
5. 将用户信息存入Session，Session Key为`Login::AUTH_KEY_LOGIN`。对Cookie写入一个flag，flag的键名为`Login::LOGIN_COOKIE_KEY`(evalogin)，值为用户ID
6. 触发`user:afterLogin`事件
7. Permission模块响应`user:afterLogin`事件，将用户角色信息存入Session，Session Key为`Login::AUTH_KEY_ROLES`

``` php
use Eva\EvaUser\Models\Login;
$login = new Login();
$login->id = 1;
$user = $login->login();
$userinfo = Login::getCurrentUser();
$rolesinfo = Login::getCurrentUserRoles();
```

#### Token登录流程：

Token登录流程1～7与Session登录一致，只是存储对象从Session更换为`Eva\EvaEngine\Service\TokenStorage`，`TokenStorage`是基于Cache的封装，提供与SessionAdater完全一致的接口。

不同之处在于Step5中不写入Cookie；在Step7中，Token登录会额外将Token信息也存入`TokenStorage`，Key为`Login::AUTH_KEY_ROLES`。

``` php
use Eva\EvaUser\Models\Login;
Login::setLoginMode(Login::LOGIN_MODE_TOKEN);
$login = new Login();
$login->id = 1;
$user = $login->login();
$userinfo = Login::getCurrentUser();
$rolesinfo = Login::getCurrentUserRoles();
```

### 基于密码登录

基于密码登录支持用户名和邮箱。每次登陆失败会将failedLogins加1，并更新loginFailedAt。failedLogins大于设定值后，屏蔽登录30秒。

``` php
$loginUser = new Login();
$loginUser->loginByPassword('username/email', 'password');
```



----

Login by Cookie Token

Cookie Token string looks like: `session_id|random_token|user_hash`

`random_token` be created when Cookie Token generated.

`user_hash` algorithm actually is `md5($salt . $user->status . $user->password)`, so if user change password or changed status(like be blocked), cookie token will be expired automatically.


``` php
$loginUser = new Login();
$loginUser->loginByCookie('cookie token string');
```

----
Login by Third part token

### Register


``` php
use Eva\EvaUser\Models\Login;
$register = new Register();
$register->assign(array(
    'username' => $this->request->getPost('username'),
    'email' => $this->request->getPost('email'),
    'password' => $this->request->getPost('password'),           
));
$register->register();
```
