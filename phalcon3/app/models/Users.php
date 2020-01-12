<?php

use Phalcon\Validation;
use Phalcon\Validation\Validator\Email as EmailValidator;

class Users extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var string
     */
    protected $email;

    /**
     *
     * @var string
     */
    protected $type;

    /**
     *
     * @var string
     */
    protected $password;

    /**
     *
     * @var string
     */
    protected $remember_token;

    /**
     *
     * @var string
     */
    protected $created_at;

    /**
     *
     * @var string
     */
    protected $email_verified_at;

    /**
     *
     * @var string
     */
    protected $updated_at;

    /**
     *
     * @var string
     */
    protected $accessed_at;

    /**
     *
     * @var string
     */
    protected $deleted_at;

    /**
     * Method to set the value of field id
     *
     * @param integer $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Method to set the value of field name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Method to set the value of field email
     *
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Method to set the value of field type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Method to set the value of field password
     *
     * @param string $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Method to set the value of field remember_token
     *
     * @param string $remember_token
     * @return $this
     */
    public function setRememberToken($remember_token)
    {
        $this->remember_token = $remember_token;

        return $this;
    }

    /**
     * Method to set the value of field created_at
     *
     * @param string $created_at
     * @return $this
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * Method to set the value of field email_verified_at
     *
     * @param string $email_verified_at
     * @return $this
     */
    public function setEmailVerifiedAt($email_verified_at)
    {
        $this->email_verified_at = $email_verified_at;

        return $this;
    }

    /**
     * Method to set the value of field updated_at
     *
     * @param string $updated_at
     * @return $this
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * Method to set the value of field accessed_at
     *
     * @param string $accessed_at
     * @return $this
     */
    public function setAccessedAt($accessed_at)
    {
        $this->accessed_at = $accessed_at;

        return $this;
    }

    /**
     * Method to set the value of field deleted_at
     *
     * @param string $deleted_at
     * @return $this
     */
    public function setDeletedAt($deleted_at)
    {
        $this->deleted_at = $deleted_at;

        return $this;
    }

    /**
     * Returns the value of field id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the value of field name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the value of field email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Returns the value of field type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the value of field password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the value of field remember_token
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->remember_token;
    }

    private function getDate($date, $format = null)
    {
        return $format && $date
            ? date($format, strtotime($date))
            : $date;
    }

    /**
     * Returns the value of field created_at
     *
     * @return string
     */
    public function getCreatedAt($format = null)
    {
        return $this->getDate($this->created_at, $format);
    }


    /**
     * Returns the value of field email_verified_at
     *
     * @return string
     */
    public function getEmailVerifiedAt($format = null)
    {
        return $this->getDate($this->email_verified_at, $format);
    }

    /**
     * Returns the value of field updated_at
     *
     * @return string
     */
    public function getUpdatedAt($format = null)
    {
        return $this->getDate($this->updated_at, $format);
    }


    /**
     * Returns the value of field accessed_at
     *
     * @return string
     */
    public function getAccessedAt($format = null)
    {
        return $this->getDate($this->accessed_at, $format);
    }


    /**
     * Returns the value of field deleted_at
     *
     * @return string
     */
    public function getDeletedAt($format = null)
    {
        return $this->getDate($this->deleted_at, $format);
    }

    /**
     * Validations and business logic
     *
     * @return boolean
     */
    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            'email',
            new EmailValidator(
                [
                    'model'   => $this,
                    'message' => 'Please enter a correct email address',
                ]
            )
        );

        return $this->validate($validator);
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("phpfw_phalcon");
        $this->setSource("users");
        $this->hasMany('id', 'Comments', 'user_id', ['alias' => 'Comments']);
        $this->hasMany('id', 'Posts', 'user_id', ['alias' => 'Posts']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'users';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Users[]|Users|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Users|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public function beforeCreate()
    {
        $now = date('Y-m-d H:i:s');
        $this->setCreatedAt($now);
        $this->setUpdatedAt($now);
        $this->setType('user');
    }

}
