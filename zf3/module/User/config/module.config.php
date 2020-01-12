<?php

namespace User;

use Zend\Router\Http\Segment;
use Zend\Router\Http\Literal;

return [
    'service_manager' => [
        'invokables' => [
            'AuthService' => 'Zend\Authentication\AuthenticationService'
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'user' => __DIR__ . '/../view',
        ],
    ],
    'controller_plugins' => [
        'factories' => [
            Controller\Plugin\AuthPlugin::class => Controller\Plugin\Factory\AuthPluginFactory::class,
            Controller\Plugin\AccessPlugin::class => Controller\Plugin\Factory\AccessPluginFactory::class,
        ],
        'aliases' => [
            'auth' => Controller\Plugin\AuthPlugin::class,
            'access' => Controller\Plugin\AccessPlugin::class,
        ],
    ],
    'view_helpers' => [
        'factories' => [
            View\Helper\Access::class => View\Helper\Factory\AccessFactory::class,
        ],
        'aliases' => [
            'access' => View\Helper\Access::class,
        ],
    ],
    'router' => [
        'routes' => [

            // Rota para as telas de administração de usuários
            'user' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/user[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\UserController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'register' => [
                'type'    => Literal::class,
                'options' => [
                    'route' => '/register',
                    'defaults' => [
                        'controller' => Controller\RegisterController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'verifyEmail' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/verifyEmail/:email/',
                    'defaults' => [
                        'controller' => Controller\RegisterController::class,
                        'action'     => 'verifyEmail'
                    ],
                ],
            ],
            'sendVerificationEmail' => [
                'type'    => Literal::class,
                'options' => [
                    'route' => '/sendVerificationEmail',
                    'defaults' => [
                        'controller' => Controller\RegisterController::class,
                        'action'     => 'sendVerificationEmail'
                    ],
                ],
            ],
            'resetPassword' => [
                'type'    => Literal::class,
                'options' => [
                    'route' => '/resetPassword',
                    'defaults' => [
                        'controller' => Controller\RegisterController::class,
                        'action'     => 'resetPassword'
                    ],
                ],
            ],
            'confirmResetPassword' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/confirmResetPassword/:email/',
                    'defaults' => [
                        'controller' => Controller\RegisterController::class,
                        'action'     => 'confirmResetPassword'
                    ],
                ],
            ],
            'login' => [
                'type'    => Literal::class,
                'options' => [
                    'route' => '/login',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action'     => 'login',
                    ],
                ],
            ],
            'logout' => [
                'type'    => Literal::class,
                'options' => [
                    'route' => '/logout',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action'     => 'logout',
                    ],
                ],
            ],
            'not-authorized' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/not-authorized',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action'     => 'notAuthorized',
                    ],
                ],
            ],
        ],
    ],
    'rbac' => [
        'roles' => [
            'guest' => ['user'],
            'user'  => ['admin'],
            'admin' => [],
        ],
        'permissions' => [
            'guest' => [
                'user.register',
                'user.sendVerificationEmail',
                'user.verifyEmail',
                'user.resetPassword',
            ],
            'user' => [ 
                
            ],
            'admin' => [
                'user.manage', 
                'user.delete', 
                'user.destroy', 
                'user.restore', 
                'user.turnIntoAdmin',
            ]
        ],
        'assertions' => [
            Model\UserAssertions::class,
        ]
    ]
]; 