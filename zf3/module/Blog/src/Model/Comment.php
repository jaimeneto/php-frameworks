<?php

namespace Blog\Model;

use DomainException;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Filter\ToInt;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Comment implements InputFilterAwareInterface
{
    public $id;
    public $text;
    public $post_id;
    public $user_id;
    public $created_at;
    public $approved_at;

    public $post_title;
    public $user_name;

    private $inputFilter;

    public function exchangeArray(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->text = $data['text'] ?? null;
        $this->post_id = $data['post_id'] ?? null;
        $this->user_id = $data['user_id'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->approved_at = $data['approved_at'] ?? null;

        // Extra fields
        $this->post_title = $data['post_title'] ?? null;
        $this->user_name = $data['user_name'] ?? null;
    }

    /**
     * Formata a data de criação do comentário
     */
    public function getCreatedAt($format = null)
    {
        return $format && $this->created_at
            ? date($format, strtotime($this->created_at))
            : $this->created_at;
    }

    /**
     * Diz se o comentário já foi aprovado
     */
    public function isApproved()
    {
        return $this->approved_at !== null;
    }

    /**
     * Formata a data de aprovação do comentário
     */
    public function getApprovedAt($format = null)
    {
        return $format && $this->approved_at
            ? date($format, strtotime($this->approved_at))
            : $this->approved_at;
    }

    public function getArrayCopy()
    {
        return [
            'id'          => $this->id,
            'text'        => $this->text,
            'post_id'     => $this->post_id,
            'user_id'     => $this->user_id,
            'created_at'  => $this->created_at,
            'approved_at' => $this->approved_at,
        ];
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new DomainException(sprintf(
            '%s não permite inserir novos filtros',
            __CLASS__
        ));
    }

    public function getInputFilter()
    {
        if ($this->inputFilter) {
            return $this->inputFilter;
        }

        $inputFilter = new InputFilter();

        $inputFilter->add([
            'name' => 'id',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'post_id',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ]
        ]);

        $inputFilter->add([
            'name' => 'text',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ]
        ]);

        $this->inputFilter = $inputFilter;
        return $this->inputFilter;
    }
}
