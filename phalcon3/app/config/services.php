<?php

use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Php as PhpEngine;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Flash\Direct as Flash;
use Phalcon\Http\Response\Cookies;
use Phalcon\Crypt;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\Dispatcher;

/**
 * Shared configuration service
 */
$di->setShared('config', function () {
    return include APP_PATH . "/config/config.php";
});

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->setShared('url', function () {
    $config = $this->getConfig();

    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
});

$di->set('translate', function () {
    $config = $this->getConfig();
    $language = $config->application->language ?? 'en';
    $langDir = $config->application->languagesDir;
    $langFile = $langDir . $language . '.php';

    $messages = file_exists($langFile)
        ? require $langFile
        : [];

    return new \Phalcon\Translate\Adapter\NativeArray(array(
        'content' => $messages
    ));
});

/**
 * Setting up the view component
 */
$di->setShared('view', function () {
    $config = $this->getConfig();

    $view = new View();
    $view->setDI($this);
    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines([
        '.volt' => function ($view) {
            $config = $this->getConfig();

            $volt = new VoltEngine($view, $this);

            $volt->setOptions([
                'compiledPath' => $config->application->cacheDir,
                'compiledSeparator' => '_'
            ]);

            $compiler = $volt->getCompiler();

            // Traduz o texto para o idioma definido
            $t = $view->getDI()->get('translate');
            $compiler->addFunction('_', function (
                $resolvedArgs, $exprArgs) use ($t) {
                return "'" . $t->_($exprArgs[0]['expr']['value']) . "'";
            });

            // Retorna uma quantidade definida de caracteres do texto
            $compiler->addFunction('truncate',function($resolvedArgs,$expArgs) use ($compiler){
                $string = $compiler->expression($expArgs[0]['expr']);
                $length = $compiler->expression($expArgs[1]['expr']);
                $append = (isset($expArgs[2])) ? $compiler->expression($expArgs[2]['expr']) : '';
            
                return "(strlen($string) < $length) ? substr($string,0,$length) : substr($string,0,$length).$append;";
            });

            return $volt;
        },
        '.phtml' => PhpEngine::class

    ]);

    return $view;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function () {
    $config = $this->getConfig();

    $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
    $params = [
        'host'     => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname'   => $config->database->dbname,
        'charset'  => $config->database->charset
    ];

    if ($config->database->adapter == 'Postgresql') {
        unset($params['charset']);
    }

    $connection = new $class($params);

    return $connection;
});


/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->setShared('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/**
 * Register the session flash service with the Twitter Bootstrap classes
 */
$di->set('flash', function () {
    return new Flash([
        'error'   => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice'  => 'alert alert-info',
        'warning' => 'alert alert-warning'
    ]);
});

/**
 * Start the session the first time some component request the session service
 */
$di->setShared('session', function () {
    $session = new SessionAdapter();
    $session->start();

    return $session;
});

$di->set('cookies', function () {
    $cookies = new Cookies();
    $cookies->useEncryption(false);
    return $cookies;
});

$di->set('crypt', function () {
    $crypt = new Crypt();
    $crypt->setCipher('aes-256-ctr');

    // Chave de segurança para a criptografia. Use a sua.
    $key = "T4\xb1\x8d\xa9\x98\x054t7w!z%C*F-Jk\x98\x05\x5c";
    $crypt->setKey($key);
    return $crypt;
});

$di->setShared('dispatcher', function () {
    $eventsManager = new EventsManager();

    // Verifica se o usuário tem permissão de acessar a página
    $eventsManager->attach('dispatch:beforeExecuteRoute', new SecurityPlugin());

    // Trata de exceções e páginas não encontradas
    $eventsManager->attach(
        'dispatch:beforeException',
        new NotFoundPlugin()
    );

    $dispatcher = new Dispatcher();
    $dispatcher->setEventsManager($eventsManager);

    return $dispatcher;
});
