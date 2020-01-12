<?php
/*
 * Modified: prepend directory path of current file, because of this file own different ENV under between Apache and command line.
 * NOTE: please remove this comment.
 */
defined('BASE_PATH') || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/../..'));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/app');

return new \Phalcon\Config([
    'database' => [
        'adapter'     => 'Mysql',
        'host'        => 'localhost',
        'username'    => 'root',
        'password'    => '',
        'dbname'      => 'phpfw_phalcon',
        'charset'     => 'utf8',
    ],
    'application' => [
        'appDir'         => APP_PATH . '/',
        'controllersDir' => APP_PATH . '/controllers/',
        'modelsDir'      => APP_PATH . '/models/',
        'migrationsDir'  => APP_PATH . '/migrations/',
        'viewsDir'       => APP_PATH . '/views/',
        'pluginsDir'     => APP_PATH . '/plugins/',
        'libraryDir'     => APP_PATH . '/library/',
        'cacheDir'       => BASE_PATH . '/cache/',
        'languagesDir'   => APP_PATH . '/languages/',

        // This allows the baseUri to be understand project paths that are not in the root directory
        // of the webpspace.  This will break if the public/index.php entry point is moved or
        // possibly if the web server rewrite rules are changed. This can also be set to a static path.
        //'baseUri'        => preg_replace('/public([\/\\\\])index.php$/', '', $_SERVER["PHP_SELF"]),
        'baseUri'        => 'http://localhost:8000/',

        'language'       => 'pt-BR',
    ],
    'acl' => [
        'roles' => [
            'admin' => 'Administrador',
            'user'  => 'Usuário',
            'guest' => 'Visitante'
        ],
        'resources' => [
            '*' => [
                'index'  => ['index', 'showPost'],
                'auth'   => ['login', 'logout'],
                'users'  => ['new', 'create'],
                'errors' => ['show403', 'show404']
            ],
            'user' => [
                'comments' => ['create', 'delete']
            ],
            'admin' => [
                'users' => ['index', 'delete', 'restore', 'turnIntoAdmin'],
                'posts' => ['index', 'new', 'edit', 'save', 'create', 'delete'],
                'comments' => ['index', 'create', 'approve', 'delete']
            ]
        ],
    ]

]);
