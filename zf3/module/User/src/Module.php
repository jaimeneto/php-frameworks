<?php

namespace User;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Session\SessionManager;
use Zend\Authentication\Storage\Session;
use Zend\Authentication\AuthenticationService;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;
use User\Mail\UserMail;

class Module implements ConfigProviderInterface
{
    public function getConfig()
    {
        // Carrega as configurações do módulo
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig()
    {
        return [
            'factories' => [
                Model\UserTable::class => function ($container) {
                    $tableGateway = $container->get(Model\UserTableGateway::class);
                    return new Model\UserTable($tableGateway);
                },
                Model\UserTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSet = new ResultSet();
                    $resultSet->setArrayObjectPrototype(new Model\User());
                    return new TableGateway('users', $dbAdapter, null, $resultSet);
                },
                Service\AuthAdapter::class => function ($container) {
                    $userTable = $container->get(Model\UserTable::class);
                    return new Service\AuthAdapter($userTable);
                },
                Service\AuthService::class => function ($container) {
                    $sessionManager = $container->get(SessionManager::class);
                    $authStorage = new Session();
                    $authAdapter = $container->get(Service\AuthAdapter::class);
                    return new AuthenticationService($authStorage, $authAdapter);
                },
                Service\Mail::class => function ($container) {
                    $config = $container->get('configuration');
                    $mailOpts = $config['mail']['transport']['options'];
                    $transport = new Smtp();
                    $transport->setOptions(new SmtpOptions($mailOpts));

                    $from = $mailOpts['connection_config']['username'];
                    $name = 'PHP Frameworks - ZF3';

                    $mail = new UserMail($transport, $from, $name);
                    return $mail;
                },
                Service\RbacManager::class => function ($container) {
                    $configs = $container->get('configuration')['rbac'];
                    $authService = $container->get(
                        Service\AuthService::class);

                    $assertionManagers = [];
                    foreach($configs['assertions'] as $assertion) {
                        $assertionManagers[] = new $assertion($authService);
                    }
                    
                    $rbacManager = new Service\RbacManager($configs, $authService, 
                        $assertionManagers);
                    return $rbacManager;
                },
            ],
        ];
    }

    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\RegisterController::class => function ($container) {
                    return new Controller\RegisterController(
                        $container->get(Model\UserTable::class),
                        $container->get(Service\Mail::class)
                    );
                },
                Controller\UserController::class => function ($container) {
                    return new Controller\UserController(
                        $container->get(Model\UserTable::class)
                    );
                },
                Controller\AuthController::class => function ($container) {
                    return new Controller\AuthController(
                        $container->get(Service\AuthService::class)
                    );
                }
            ],
        ];
    }

}
