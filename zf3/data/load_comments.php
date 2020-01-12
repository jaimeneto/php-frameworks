<?php

$pdo = new PDO('mysql:host=localhost;dbname=phpfw_zf3', 'root', '');

// Pega os ids dos usuários cadastrados
$stmtUsers = $pdo->query('SELECT id FROM users');
$users = [];
while ($userId = $stmtUsers->fetchColumn()) {
    $users[] = $userId;
}

// Pega os ids dos posts cadastrados
$stmtPosts = $pdo->query('SELECT id FROM posts');
$posts = [];
while ($postId = $stmtPosts->fetchColumn()) {
    $posts[] = $postId;
}

// Define alguns comentários que serão repetidos nos posts
$comments = [
    'Commodi quis et consequuntur impedit. Ut dolorem et et vel tempore eligendi. Nulla similique laudantium nemo et officiis harum.',
    'Nesciunt dignissimos quia tenetur iste soluta non porro porro. Fugiat et et nihil ut. Repellendus repellat odio mollitia.',
    'Vero aut et ad est rem distinctio hic. Voluptatem impedit quae nihil fugiat ex repellendus est. Illum nemo tenetur ea.',
    'Incidunt rerum qui laboriosam sed omnis qui. Maiores magni repellat sit ut est nisi sit eos. Quia debitis dolorem repellendus tenetur. Quos vero necessitatibus est hic saepe architecto rerum quis.',
    'Omnis dolore molestias nam ut aut. Accusantium maiores nam ducimus unde veritatis voluptate autem aperiam. Iure pariatur iste aspernatur soluta sit nemo consequatur ipsam.',
    'Ut asperiores ipsum cumque incidunt qui. Dicta dicta omnis nesciunt magni odit. Porro non aut quibusdam labore eos. Neque molestiae sed animi velit.',
    'Dolor rerum aut quia earum nihil laboriosam iste non. Qui sit voluptatem non et. Eveniet tempora ut iste rerum ab aut sapiente.'
];

$now = date('Y-m-d H:i:s'); // Data e hora atuais

$values = [];
foreach ($posts as $postId) {
    foreach ($comments as $comment) {
        // Pega um id de usuário aleatório
        $userId = $users[array_rand($users)];

        // Aleatoriamente cria comentários aprovados ou não
        $approvedAt = rand(0, 1) > 0 ? "'{$now}'" : 'null';

        $values[] = "({$postId}, '{$comment}', {$userId}, '{$now}', {$approvedAt})";
    }
}

$sql = "INSERT INTO `comments` (`post_id`, `text`, `user_id`, `created_at`, `approved_at`) VALUES "
    . implode(',', $values) . ';';

$pdo->exec($sql);