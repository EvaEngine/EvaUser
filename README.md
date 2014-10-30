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


### 基于Cookie登录 

产生Cookie String

``` php
$user = new Login();
$loginUser = $user->loginByPassword('username', 'password');
//获得Token String并存入数据库user_tokens表
$token = $user->getRememberMeToken();
//将Token String存入Cookie
$cookies = $this->cookies->set(Login::LOGIN_COOKIE_REMEMBER_KEY, $token, time() + $user->getRememberMeTokenExpires());
```

Cookie String的构成为 `session_id|random_token|user_hash`，其中
- session_id 获得方法：`$this->getDI()->getSession()->getId()`  目的是允许复数个Session登录，并且便于管理
- token      `md5(uniqid(rand(), true))`  防止碰撞
- user_hash  `md5($this->tokenSalt . $userinfo->status . $userinfo->password)`  

用户登录成功并勾选“记住密码”后，将Cookie String存入Cookie，最后cookie string形如：

```
realm:hnmijl3c50a75704o0v4lvb432%7C46b62e13fd4ca872c252b8585226e473%7C9fd6b849d330b3aba1f73c6153feee88;
```

PHPSESSID随session过期而过期，realm则设置一个非常长的过期时间。

#### 重新登录过程：

在所有页面监听`Dispatch:afterExecuteRoute`事件，处理流程如下：

- 如果没有realm，不做处理
- 如果用户已经登录，不做处理
- 否则通过realm登录

登录过程：

```
$login = new Login();
$login->loginByCookie($cookie->get(Login::LOGIN_COOKIE_REMEMBER_KEY));
```

登录细节：

1. 使用|将Cookie String分割
2. 使用分割后的字符作为条件查询user_tokens表
3. 将Cookie String中的user_hash与当前数据表中用户实际的user_hash进行对比，如果不一致则不进行登录，代表用户状态或者密码发生改变，此时自动登录强制失效
4. 使用数据库中的userid自动登录
5. 更新数据库中SessionID为当前用户SessionID（没有做）

参考资料

http://jaspan.com/improved_persistent_login_cookie_best_practice


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
