<?php

namespace Blog;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

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
                Model\PostTable::class => function ($container) {
                    $tableGateway =
                        $container->get(Model\PostTableGateway::class);
                    return new Model\PostTable($tableGateway);
                },
                Model\PostTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $commentTable = $container->get(Model\CommentTable::class);
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Post($commentTable));
                    return new TableGateway('posts', $dbAdapter, null, $resultSetPrototype);
                },
                Model\CommentTable::class => function ($container) {
                    $tableGateway =
                        $container->get(Model\CommentTableGateway::class);
                    return new Model\CommentTable($tableGateway);
                },
                Model\CommentTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSet = new ResultSet();
                    $resultSet->setArrayObjectPrototype(new Model\Comment());
                    return new TableGateway('comments', $dbAdapter, null, $resultSet);
                },
            ],
        ];
    }

    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\PostController::class => function ($container) {
                    return new Controller\PostController(
                        $container->get(Model\PostTable::class)
                    );
                },
                Controller\CommentController::class => function ($container) {
                    return new Controller\CommentController(
                        $container->get(Model\CommentTable::class)
                    );
                },
            ],
        ];
    }
}
