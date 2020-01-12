<?php

namespace Blog\Form;

use Zend\Form\Form;

class PostForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('post');

        //$this->setAttribute('method', 'POST');

        $this->add([
            'name' => 'id',
            'type' => 'hidden',
        ]);

        $this->add([
            'name' => 'title',
            'type' => 'text',
            'options' => [
                'label' => 'Título',
            ],
        ]);

        $this->add([
            'name' => 'text',
            'type' => 'textarea',
            'options' => [
                'label' => 'Texto',
            ],
        ]);

        $this->add([
            'type' => 'csrf',
            'name' => 'csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 600
                ]
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Salvar',
                'id'    => 'submitbutton',
            ],
        ]);

        $this->add([
            'name' => 'cancel',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Cancelar',
                'id'    => 'cancelbutton',
            ],
        ]);
    }
}