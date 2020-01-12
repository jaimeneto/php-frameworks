<?php

class Comments extends \Phalcon\Mvc\Model
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
    protected $text;

    /**
     *
     * @var integer
     */
    protected $post_id;

    /**
     *
     * @var integer
     */
    protected $user_id;

    /**
     *
     * @var string
     */
    protected $created_at;

    /**
     *
     * @var string
     */
    protected $approved_at;

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
     * Method to set the value of field text
     *
     * @param string $text
     * @return $this
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Method to set the value of field post_id
     *
     * @param integer $post_id
     * @return $this
     */
    public function setPostId($post_id)
    {
        $this->post_id = $post_id;

        return $this;
    }

    /**
     * Method to set the value of field user_id
     *
     * @param integer $user_id
     * @return $this
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;

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
     * Method to set the value of field approved_at
     *
     * @param string $approved_at
     * @return $this
     */
    public function setApprovedAt($approved_at)
    {
        $this->approved_at = $approved_at;

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
     * Returns the value of field text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Returns the value of field post_id
     *
     * @return integer
     */
    public function getPostId()
    {
        return $this->post_id;
    }

    /**
     * Returns the value of field user_id
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Returns the value of field created_at
     *
     * @return string
     */
    public function getCreatedAt($format = null)
    {
        return $format && $this->created_at
            ? date($format, strtotime($this->created_at))
            : $this->created_at;
    }

    /**
     * Returns the value of field approved_at
     *
     * @return string
     */
    public function getApprovedAt($format = null)
    {
        return $format && $this->approved_at
            ? date($format, strtotime($this->approved_at))
            : $this->approved_at;
    }


    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("phpfw_phalcon");
        $this->setSource("comments");
        $this->belongsTo('post_id', '\Posts', 'id', ['alias' => 'Posts']);
        $this->belongsTo('user_id', '\Users', 'id', ['alias' => 'Users']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'comments';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Comments[]|Comments|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Comments|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public function getPost()
    {
        return $this->posts;
    }

    public function getUser()
    {
        return $this->users;
    }

    public function beforeValidationOnCreate()
    {
        $now = date('Y-m-d H:i:s');
        $this->setCreatedAt($now);
    }
    
}
