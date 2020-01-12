<?php

namespace User\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Authentication\AuthenticationService;

class AuthPlugin extends AbstractPlugin
{
    private $authService;

    public function __construct(AuthenticationService $authService)
    {
        $this->authService = $authService;
    }

    public function __invoke()
    {
        // Retorna os dados do usuário autenticado
        return $this->authService->getIdentity();
    }
}