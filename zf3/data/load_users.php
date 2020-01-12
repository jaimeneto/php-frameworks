<?php

$users = [
    ['Administrador', 'admin@email.com', 'admin'],
    ['User', 'user@email.com', 'user'],
    ['Fulano', 'fulano@email.com', 'admin'],
    ['Sicrano', 'sicrano@email.com', 'user'],
    ['Beltrano', 'beltrano@email.com', 'user']
];

$now = date('Y-m-d H:i:s'); // Data e hora atuais

// Hash para 'zendframework' utilizando BCRYPT
$password = '$2y$10$Tfj5yKuOQTbjxulUNh8LhO9cMr/Cj3WD1gZRx36JZdE9VAQ03l9ve';

$values = [];

foreach ($users as $user) {
   $name = $user[0]; $email = $user[1]; $type = $user[2];
   $values[] = "('{$name}', '{$email}', '{$type}', '{$password}', " 
             . "'{$now}', '{$now}')";
}

$sql = "INSERT INTO `users` (`name`, `email`, `type`, `password`," 
     . " `created_at`, `updated_at`) VALUES " . implode(',', $values) . ';';

$pdo->exec($sql);