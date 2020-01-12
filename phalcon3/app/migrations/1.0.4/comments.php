<?php

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class CommentsMigration_104
 */
class CommentsMigration_104 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable(
            'comments',
            [
                'columns' => [
                    new Column(
                        'id',
                        [
                            'type' => Column::TYPE_BIGINTEGER,
                            'unsigned' => true,
                            'notNull' => true,
                            'autoIncrement' => true,
                            'size' => 20,
                            'first' => true
                        ]
                    ),
                    new Column(
                        'text',
                        [
                            'type' => Column::TYPE_TEXT,
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'id'
                        ]
                    ),
                    new Column(
                        'post_id',
                        [
                            'type' => Column::TYPE_BIGINTEGER,
                            'unsigned' => true,
                            'notNull' => true,
                            'size' => 20,
                            'after' => 'text'
                        ]
                    ),
                    new Column(
                        'user_id',
                        [
                            'type' => Column::TYPE_BIGINTEGER,
                            'unsigned' => true,
                            'notNull' => true,
                            'size' => 20,
                            'after' => 'post_id'
                        ]
                    ),
                    new Column(
                        'created_at',
                        [
                            'type' => Column::TYPE_TIMESTAMP,
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'user_id'
                        ]
                    ),
                    new Column(
                        'approved_at',
                        [
                            'type' => Column::TYPE_TIMESTAMP,
                            'size' => 1,
                            'after' => 'created_at'
                        ]
                    )
                ],
                'indexes' => [
                    new Index('PRIMARY', ['id'], 'PRIMARY'),
                    new Index('comments_post_id_foreign', ['post_id'], null),
                    new Index('comments_user_id_foreign', ['user_id'], null)
                ],
                'references' => [
                    new Reference(
                        'comments_post_id_foreign',
                        [
                            'referencedTable' => 'posts',
                            'referencedSchema' => 'phpfw_phalcon',
                            'columns' => ['post_id'],
                            'referencedColumns' => ['id'],
                            'onUpdate' => 'RESTRICT',
                            'onDelete' => 'CASCADE'
                        ]
                    ),
                    new Reference(
                        'comments_user_id_foreign',
                        [
                            'referencedTable' => 'users',
                            'referencedSchema' => 'phpfw_phalcon',
                            'columns' => ['user_id'],
                            'referencedColumns' => ['id'],
                            'onUpdate' => 'RESTRICT',
                            'onDelete' => 'CASCADE'
                        ]
                    )
                ],
                'options' => [
                    'TABLE_TYPE' => 'BASE TABLE',
                    'AUTO_INCREMENT' => '1',
                    'ENGINE' => 'InnoDB',
                    'TABLE_COLLATION' => 'utf8mb4_unicode_ci'
                ],
            ]
        );
    }

    /**
     * Run the migrations
     *
     * @return void
     */
    public function up()
    {
        $now = date('Y-m-d H:i:s'); // Data e hora atuais

        $sql = "SELECT id FROM users";
        $users = self::$connection->fetchAll($sql);
        $usersIds = [];
        foreach ($users as $user) {
            $usersIds[] = $user['id'];
        }

        $sql = "SELECT id FROM posts";
        $posts = self::$connection->fetchAll($sql);

        for ($i = 1; $i <= 10; $i++) {
            $comments = [
                'Commodi quis et consequuntur impedit. Ut dolorem et et vel tempore eligendi. Nulla similique laudantium nemo et officiis harum.',
                'Nesciunt dignissimos quia tenetur iste soluta non porro porro. Fugiat et et nihil ut. Repellendus repellat odio mollitia.',
                'Vero aut et ad est rem distinctio hic. Voluptatem impedit quae nihil fugiat ex repellendus est. Illum nemo tenetur ea.',
                'Incidunt rerum qui laboriosam sed omnis qui. Maiores magni repellat sit ut est nisi sit eos. Quia debitis dolorem repellendus tenetur. Quos vero necessitatibus est hic saepe architecto rerum quis.',
                'Omnis dolore molestias nam ut aut. Accusantium maiores nam ducimus unde veritatis voluptate autem aperiam. Iure pariatur iste aspernatur soluta sit nemo consequatur ipsam.',
                'Ut asperiores ipsum cumque incidunt qui. Dicta dicta omnis nesciunt magni odit. Porro non aut quibusdam labore eos. Neque molestiae sed animi velit.',
                'Dolor rerum aut quia earum nihil laboriosam iste non. Qui sit voluptatem non et. Eveniet tempora ut iste rerum ab aut sapiente.'
            ];

            foreach ($posts as $post) {
                foreach ($comments as $comment) {
                    $userId = $usersIds[array_rand($usersIds)];
                    self::$connection->insert(
                        'comments',
                        [
                            $comment, $post['id'], $userId, $now,
                            (bool)rand(0, 1) ? $now : null
                        ],
                        [
                            'text', 'post_id', 'user_id', 'created_at',
                            'approved_at'
                        ]
                    );
                }
            }
        }
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down()
    { }
}
