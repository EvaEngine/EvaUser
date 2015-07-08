<?php

namespace Eva\EvaUser\Forms;

use \Eva\EvaEngine\Mvc\Model;
use Eva\EvaEngine\Exception;

/**
 * @SWG\Model(id="UserVerification")
 */
class UserVerification extends Form
{
    /**
     * @SWG\Property(
     * type="string",
     * required=true,
     * description="<pre><h2>请填写需要验证的手机号码，如：&quot;13712345678&quot;<br><br>或邮箱地址，如：&quot;abc@123.com&quot;</h2></pre>"),
     * required=true
     * @var
     */
    public $identifier;
    /**
     * @SWG\Property(
     * type="string",
     * required=true,
     * description="<pre><h2>请指明需要验证的是手机：&quot;MOBILE&quot;<br><br>还是邮箱：&quot;EMAIL&quot;。</h2></pre>",
     * enum="['EMAIL', 'MOBILE']"),
     * required=true
     * @var
     */
    public $identifierType;

    /**
     * @SWG\Property(
     * name="role",type="string",
     * required=true,
     * enum="['USR_REGISTER', 'USR_EDIT']",
     * description="用来区分验证场景，以便客制化验证信息模板。<pre><h2>注册新用户请填：&quot;USR_REGISTER&quot;<br><br>更改用户信息请填：&quot;USR_EDIT&quot;</h2></pre>"),
     * required=true
     * @var
     */
    public $role;


}
