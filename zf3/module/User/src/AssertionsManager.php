<?php

namespace User;

use Zend\Permissions\Rbac\AssertionInterface;
use Zend\Permissions\Rbac\RoleInterface;
use Zend\Permissions\Rbac\Rbac;

abstract class AssertionsManager implements AssertionInterface
{
    protected $authService;
    protected $role;
    protected $params;

    public function __construct($authService)
    {
        $this->authService = $authService;
    }

    private function getMethodName($permission)
    {
        return 'assert' . str_replace('.', '', ucwords($permission, '.'));
    }

    public function hasMethod($permission)
    {
        $method = $this->getMethodName($permission);
        return method_exists($this, $method);
    }

    public function setParams(array $params)
    {
        $this->params = $params;
    }

    public function assert(Rbac $rbac, RoleInterface $role, 
       string $permission) : bool
   {
       // Verifica primeiramente se o usuário autenticado tem permissão
       if ($rbac->isGranted($role, $permission)) {
           $this->role = $role;

           // Verifica se existe um método específico para a permissão
           if (!$this->hasMethod($permission)) {
               return true;
           }

           // Executa o método específico para a permissão
           $method = $this->getMethodName($permission);
           return $this->$method();
       }
       return false;
   }

    // Nas classes que herdam desta, crie métodos usando o padrão 
    // assertNomeDaPermissao() para a permissão nome.da.permissao
}