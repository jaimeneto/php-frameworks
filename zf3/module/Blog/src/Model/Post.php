<?php

namespace Blog\Model;

use DomainException;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Filter\ToInt;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\StringLength;

class Post implements InputFilterAwareInterface
{
    public $id;
    public $title;
    public $text;
    public $user_id;
    public $created_at;
    public $updated_at;

    public $comments_count;
    public $user_name;

    private $inputFilter;

    private $commentTable;

    public function __construct(CommentTable $commentTable = null)
    {
        $this->commentTable = $commentTable;
    }

    public function getComments()
    {
        if (null === $this->commentTable) {
            throw new RuntimeException(
                'Erro ao tentar consultar comentários. ' .
                    'CommentTable não definido.'
            );
        }

        return $this->commentTable->fetchAll(['post_id' => $this->id], 'id ASC');
    }


    public function exchangeArray(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->title = $data['title'] ?? null;
        $this->text = $data['text'] ?? null;
        $this->user_id = $data['user_id'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
        $this->comments_count = $data['comments_count'] ?? null;
        $this->user_name = $data['user_name'] ?? null;
    }

    public function getArrayCopy()
    {
        return [
            'id'         => $this->id,
            'title'      => $this->title,
            'text'       => $this->text,
            'user_id'    => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Formata a data de criação do post
     */
    public function getCreatedAt($format = null)
    {
        return $format && $this->created_at
            ? date($format, strtotime($this->created_at))
            : $this->created_at;
    }

    /**
     * Formata a data da última alteração do post
     */
    public function getUpdatedAt($format = null)
    {
        return $format && $this->updated_at
            ? date($format, strtotime($this->updated_at))
            : $this->updated_at;
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
            'name' => 'title',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 255,
                    ],
                ]
            ],
        ]);

        $inputFilter->add([
            'name' => 'text',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 100
                    ],
                ],
            ],
        ]);

        $this->inputFilter = $inputFilter;
        return $this->inputFilter;
    }
}
