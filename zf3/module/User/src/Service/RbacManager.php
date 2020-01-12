<?php

namespace User\Service;

use Zend\Permissions\Rbac\Rbac;

/**
 * This service is responsible for initialzing RBAC (Role-Based Access Control).
 */
class RbacManager
{
    /**
     * RBAC Configs
     * @var array
     */
    private $configs;

    /**
     * RBAC service.
     * @var Zend\Permissions\Rbac\Rbac
     */
    private $rbac;

    /**
     * Auth service.
     * @var Zend\Authentication\AuthenticationService 
     */
    private $authService;

    /**
     * Assertion managers.
     * @var array
     */
    private $assertionManagers = [];

    public function __construct(array $configs, $authService, $assertionManagers)
    {
        $this->configs = (object) $configs;
        $this->authService = $authService;
        $this->assertionManagers = $assertionManagers;
    }

    /**
     * Initializes the RBAC container.
     */
    public function init()
    {
        // Create Rbac container.
        $rbac = new Rbac();

        // Add parent roles automatically if not present when calling addRole()
        $rbac->setCreateMissingRoles(true);

        foreach ($this->configs->roles as $roleName => $parentRoleNames) {
            $rbac->addRole($roleName, $parentRoleNames);

            $permissions = $this->configs->permissions[$roleName];
            foreach ($permissions as $permission) {
                $rbac->getRole($roleName)->addPermission($permission);
            }
        }

        // Associa os papéis filhos aos pais
        foreach ($this->configs->roles as $roleName => $parentRoleNames) {
            foreach($parentRoleNames as $parentRoleName) {
                $rbac->getRole($parentRoleName)
                    ->addChild($rbac->getRole($roleName));
            }
        }

        $this->rbac = $rbac;
    }

    /**
     * Checks whether the given user has permission.
     * @param User|null $user
     * @param string $permission
     * @param array|null $params
     */
    public function isGranted($user, $permission, $params = null)
    {
        // Inicia o container se ainda não tiver sido inicializado
        if ($this->rbac == null) {
            $this->init();
        }

        // Pega os dados de autenticação do usuário
        $identity = $this->authService->getIdentity();

        // Por padrão o tipo de usuário é visitante
        $roleName = 'guest';
        
        // Se a verificação for pra um usuário específico
        if ($user) {            
            $roleName = $user->type;  // Verifica o tipo do usuário
        } 

        // Se a verificação não for para um usuário específico
        // verifica se o usuário está autenticado
        elseif ($this->authService->hasIdentity()) {
            // Verifica o tipo do usuário autenticado
            $roleName = $this->authService->getIdentity()->type;
        }

        // Se o tipo de usuário não existe nega a permissão
        if (!$this->rbac->hasRole($roleName)) {
            return false;
        }

        $role = $this->rbac->getRole($roleName);
        
        // Verifica se o tipo de usuário tem permissão
        if ($this->rbac->isGranted($role, $permission)) {
            foreach ($this->assertionManagers as $assertionManager) {
                if ($assertionManager->hasMethod($permission)) {
                    $assertionManager->setParams($params);
                    return $assertionManager->assert($this->rbac, $role, $permission);
                }
            }
            return true;
        }

        return false;
    }
}
