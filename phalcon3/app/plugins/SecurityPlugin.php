<?php

use Phalcon\Acl;
use Phalcon\Acl\Role;
use Phalcon\Acl\Resource;
use Phalcon\Acl\Adapter\Memory as AclList;
use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher;
use Phalcon\DI;

class SecurityPlugin extends Plugin
{
    public function getAcl()
    {
        // Cria a lista de controle de acesso
        $acl = new AclList();

        // Faz com que o padrão seja negar a permissão 
        // caso não esteja definida
        $acl->setDefaultAction(Acl::DENY);

        // Pega a lista de permissões do arquivo de configurações
        $config = $this->getDi()->getConfig()->acl;

        // Cria os perfis de usuário
        $roles = $config->roles;
        foreach ($roles as $name => $description) {
            $acl->addRole(new Role($name, $description));
        }

        // Cria os recursos
        $resources = $config->resources->toArray();
        foreach ($resources as $role => $permissions) {
            foreach ($permissions as $resource => $actions) {
                $acl->addResource(new Resource($resource), $actions);

                // Dá as permissões
                foreach ($actions as $action) {
                    $acl->allow($role, $resource, $action);
                }
            }
        }

        // Salva a lista de controle de acesso na sessão
        return $this->persistent->acl = $acl;
    }

    /**
     * Executa antes de qualquer controller/action na aplicação
     */
    public function beforeExecuteRoute(
        Event $event,
        Dispatcher $dispatcher
    ) {
        // Verifica se o usuário está logado
        $auth = $this->session->get('auth');

        // Se não estiver logado, assume que é visitante
        $role = $auth ? $auth->type : 'guest';

        // Verifica qual o controller e action atuais
        $controller = $dispatcher->getControllerName();
        $action     = $dispatcher->getActionName();

        // Obtém a lista de controle de acesso
        $acl = $this->getAcl();

        // Verifica se o usuário tem acesso ao controller (recurso)
        $allowed = $acl->isAllowed($role, $controller, $action);
        if (!$allowed) {
            if ($role === 'guest') {
                // Se não tiver redireciona para a tela de login
                // com uma mensagem de erro
                $this->flash->error('Acesso negado');
                $dispatcher->forward([
                    'controller' => 'auth',
                    'action'     => 'login',
                    'params'     => [
                        // Passa a url atual como parâmetro para
                        // redirecionar para ela quando fizer login
                        DI::getDefault()->get('router')->getRewriteUri()
                    ]
                ]);
            } else {
                // Redireciona para uma tela de erro de permissão
                $dispatcher->forward([
                    'controller' => 'errors',
                    'action'     => 'show403'
                ]);
            }

            return false; // Encerra a operação atual
        }
    }
}
