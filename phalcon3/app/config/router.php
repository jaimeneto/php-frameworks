<?php

$router = $di->getRouter();

$router->add('/register', [
    'controller' => 'users',
    'action'     => 'new',
]);

$router->add('/login/:params', [
    'controller' => 'auth',
    'action'     => 'login',
    'params'     => 1
]);

$router->add('/logout', [
    'controller' => 'auth',
    'action'     => 'logout',
]);

$router->add('/post/:params', [
    'controller' => 'index',
    'action'     => 'showPost',
    'params'     => 1
]);

$router->handle();
