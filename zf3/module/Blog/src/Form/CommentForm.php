<?php

namespace Blog\Form;

use Zend\Form\Form;

class CommentForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('comment');

        $this->add([
            'name' => 'id',
            'type' => 'hidden',
        ]);

        $this->add([
            'name' => 'post_id',
            'type' => 'hidden',
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
            'name' => 'reset',
            'type' => 'button',
            'attributes' => [
                'value' => 'Limpar',
                'id'    => 'resetbutton',
            ],
        ]);
    }
}
