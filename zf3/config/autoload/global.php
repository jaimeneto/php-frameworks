<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

use Zend\Session;

return [
    'db' => [
        'driver'   => 'Pdo',
        'dsn'      => 'mysql:dbname=phpfw_zf3;host=localhost;charset=utf8',
        'username' => 'root',
        'password' => ''
    ],
    'mail' => [
        'transport' => [
            'options' => [
                'name'              => 'yahoo.com',
                'host'              => 'digite-aqui-o-host',
                'port'              => '587',
                'connection_class'  => 'plain',
                'connection_config' => [
                    'username' => 'digite-aqui-seu-email',
                    'password' => 'digite-aqui-sua-senha',
                    'ssl'      => 'tls'
                ],
            ],  
        ],
        'from_name' => 'PHP Frameworks - ZF3',
        'url'       => 'digite-aqui-a-url-da-aplicacao'
    ],
    'translator' => [
        'locale' => 'pt_BR',
        'translation_file_patterns' => [
            [
                'type'     => 'phparray',
                'base_dir' => getcwd() .  '/data/language',
                'pattern'  => '%s.php',
            ],
        ],
    ],
    'view_helpers' => [
        'invokables' => [
            '_' => \Zend\I18n\View\Helper\Translate::class
        ],
    ],
    'navigation' => [
        'default' => [
            'admin' => [
                'id'      => 'admin',
                'uri'     => '#',
                'pages'   => [
                    [
                        'label' => _('Users'),
                        'route' => 'user',
                    ], [
                        'label' => _('Posts'),
                        'route' => 'post',
                        'pages' => [
                            [
                                'label'  => _('Add post'),
                                'route'  => 'post',
                                'action' => 'add',
                            ],
                            [
                                'label'  => _('Edit post'),
                                'route'  => 'post',
                                'action' => 'edit',
                            ],
                            [
                                'label'  => _('View post'),
                                'route'  => 'post',
                                'action' => 'view',
                            ],
                        ],
                    ], [
                        'label' => _('Comments'),
                        'route' => 'comment'
                    ],
                ],
            ],
            'guest' => [
                'id'      => 'guest',
                'uri'     => '#',
                'pages'   => [
                    [
                        'label' => _('Login'),
                        'route' => 'login',
                    ],
                    [
                        'label' => _('Register'),
                        'route' => 'register',
                    ],
                ]
            ],
            'user' => [
                'id'      => 'user',
                'uri'     => '#',
                'pages'   => [
                    [
                        'label' => _('User'),
                        'uri'   => '#',
                        'id'    => 'user-name'
                    ],
                    [
                        'label' => _('Logout'),
                        'route' => 'logout',
                    ],
                ]
            ]
        ]
    ],
    // 'session_manager' => [
    //     'config' => [
    //         'class' => Session\Config\SessionConfig::class,
    //         'options' => [
    //             'name' => 'phpfw_zf3',
    //             // 'cookie_lifetime' => 60 * 60 * 1,       // 1 hora
    //             // 'gc_maxlifetime'  => 60 * 60 * 24 * 30,   // 30 dias
    //         ]
    //     ],
    //     'storage' => Session\Storage\SessionArrayStorage::class,
    //     'validators' => [
    //         Session\Validator\RemoteAddr::class,
    //         Session\Validator\HttpUserAgent::class,
    //     ],
    // ]
];
