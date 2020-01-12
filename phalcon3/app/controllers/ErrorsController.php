<?php

class ErrorsController extends ControllerBase
{

    public function initialize()
    {
        $this->tag->setTitle('Oops!');
        parent::initialize();
    }

    /**
     * Página não encontrada
     */
    public function show404Action()
    { }

    /**
     * Permissão negada
     */
    public function show403Action()
    { }
    
}
