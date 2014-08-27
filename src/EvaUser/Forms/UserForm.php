<?php
namespace Eva\EvaUser\Forms;

use Eva\EvaEngine\Form;

class UserForm extends Form
{
    /**
     * @Type(Hidden)
     * @var integer
     */
    public $id;

    /**
     * @Filter(trim)
     * @var string
     */
    public $username;

    /**
     *
     * @Type(Email)
     * @var string
     */
    public $email;

    /**
     *
     * @var string
     */
    public $mobile;

    /**
     *
     * @Type(Select)
     * @Option(inactive=Inactive)
     * @Option(active=Active)
     * @Option(deleted=Deleted)
     * @var string
     */
    public $status;

    /**
     *
     * @Type(Select)
     * @Option(basic=Basic)
     * @Option(premium=Premium)
     * @Option(etc=Etc)
     * @var string
     */
    public $accountType;

    /**
     *
     * @var string
     */
    public $screenName;

    /**
     *
     * @var string
     */
    public $firstName;

    /**
     *
     * @var string
     */
    public $lastName;

    /**
     *
     * @Type(Password)
     * @var string
     */
    public $password;

    /**
     *
     * @var string
     */
    public $oldPassword;

    /**
     *
     * @var string
     */
    public $gender;

    /**
     *
     * @var integer
     */
    public $avatarId;

    /**
     *
     * @var string
     */
    public $avatar;

    /**
     *
     * @var string
     */
    public $timezone;

    /**
     *
     * @var string
     */
    public $language;

    /**
     *
     * @var string
     */
    public $emailStatus;

    /**
     *
     * @var integer
     */
    public $emailConfirmedAt;

    /**
     *
     * @var integer
     */
    public $createdAt;

    /**
     *
     * @var integer
     */
    public $loginAt;

    /**
     *
     * @var string
     */
    public $failedLogins;

    /**
     *
     * @var integer
     */
    public $loginFailedAt;

    /**
     *
     * @var string
     */
    public $activationHash;

    /**
     *
     * @var integer
     */
    public $activedAt;

    /**
     *
     * @var string
     */
    public $passwordResetHash;

    /**
     *
     * @var integer
     */
    public $passwordResetAt;

    /**
     *
     * @var string
     */
    public $providerType;

    public function initialize($entity = null, $options = null)
    {
    }
}
