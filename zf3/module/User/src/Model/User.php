<?php

namespace User\Model;

use DomainException;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Filter\ToInt;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\StringLength;
use Zend\Validator\EmailAddress;
use Zend\Validator\Identical;

class User implements InputFilterAwareInterface
{
    public $id;
    public $name;
    public $email;
    public $type;
    public $password;
    public $remember_token;
    public $created_at;
    public $email_verified_at;
    public $updated_at;
    public $accessed_at;
    public $deleted_at;

    private $inputFilter;

    public function exchangeArray(array $data)
    {
        $this->id                = $data['id'] ?? null;
        $this->name              = $data['name'] ?? null;
        $this->email             = $data['email'] ?? null;
        $this->type              = $data['type'] ?? null;
        $this->password          = $data['password'] ?? null;
        $this->remember_token    = $data['remember_token'] ?? null;
        $this->created_at        = $data['created_at'] ?? null;
        $this->email_verified_at = $data['email_verified_at'] ?? null;
        $this->updated_at        = $data['updated_at'] ?? null;
        $this->accessed_at       = $data['accessed_at'] ?? null;
        $this->deleted_at        = $data['deleted_at'] ?? null;
    }

    /**
     * Formata a data de criação do usuário
     */
    public function getCreatedAt($format = null)
    {
        return $format && $this->created_at
            ? date($format, strtotime($this->created_at))
            : $this->created_at;
    }

    /**
     * Formata a data de validação do email 
     */
    public function getEmailVerifiedAt($format = null)
    {
        return $format && $this->email_verified_at
            ? date($format, strtotime($this->email_verified_at))
            : $this->email_verified_at;
    }

    /**
     * Formata a data da última alteração do usuário
     */
    public function getUpdatedAt($format = null)
    {
        return $format && $this->updated_at
            ? date($format, strtotime($this->updated_at))
            : $this->updated_at;
    }

    /**
     * Formata a data do último acesso do usuário
     */
    public function getAccessedAt($format = null)
    {
        return $format && $this->accessed_at
            ? date($format, strtotime($this->accessed_at))
            : $this->accessed_at;
    }

    /**
     * Formata a data que o usuário foi enviado para a lixeira
     */
    public function getDeletedAt($format = null)
    {
        return $format && $this->deleted_at
            ? date($format, strtotime($this->deleted_at))
            : $this->deleted_at;
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
            'name' => 'name',
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
            'name' => 'email',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => EmailAddress::class,
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'password',
            'required' => true,
        ]);

        $inputFilter->add([
            'name' => 'password_confirm',
            'required' => true,
            'validators' => [
                [
                    'name'    => Identical::class,
                    'options' => [
                        'token' => 'password',
                    ],
                ],
            ],
        ]);

        $this->inputFilter = $inputFilter;
        return $this->inputFilter;
    }
}
