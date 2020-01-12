<?php

// Pega os ids dos administradores cadastrados
$stmtUsers = $pdo->query("SELECT id FROM users WHERE type = 'admin'");
$users = [];
while ($userId = $stmtUsers->fetchColumn()) {
   $users[] = $userId;
}

// Define alguns posts com título e texto que serão cadastrados
$posts = [
   ['Lorem Ipsum', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'],
   ['de Finibus Bonorum et Malorum (1.10.32)', 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?'],
   ['de Finibus Bonorum et Malorum (1.10.33)', 'At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.']
];

$now = date('Y-m-d H:i:s'); // Data e hora atuais

$values = [];

// Cadastra 10 vezes cada post
for ($i = 1; $i <= 10; $i++) {
  foreach($posts as $post) {
    $title = $post[0] . ' ' . $i;
    $text = $post[1];

    // Pega um id de usuário aleatório
    $userId = $users[array_rand($users)];

    $values[] = "('{$title}', '{$text}', {$userId}, '{$now}', '{$now}')";
  }
}

$sql = "INSERT INTO `posts` (`title`, `text`, `user_id`, `created_at`, `updated_at`) VALUES " . implode(',', $values) . ';';

$pdo->exec($sql);