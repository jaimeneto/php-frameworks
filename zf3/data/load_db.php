<?php
$schema = file_get_contents(__DIR__ . '/schema.sql');
$pdo = new PDO('mysql:host=localhost;dbname=phpfw_zf3', 'root', '');
$statement = $pdo->exec($schema);