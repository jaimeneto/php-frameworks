<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\DataFixtures\UserFixtures;
use App\DataFixtures\PostFixtures;
use App\Entity\Comment;

class CommentFixtures extends Fixture  implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [
            UserFixtures::class,
            PostFixtures::class
        ];
    }

    public function load(ObjectManager $manager)
    {
        // Define alguns comentários que serão 
        // repetidos nos posts
        $comments = [
            'Commodi quis et consequuntur impedit. Ut dolorem et et vel tempore eligendi. Nulla similique laudantium nemo et officiis harum.',
            'Nesciunt dignissimos quia tenetur iste soluta non porro porro. Fugiat et et nihil ut. Repellendus repellat odio mollitia.',
            'Vero aut et ad est rem distinctio hic. Voluptatem impedit quae nihil fugiat ex repellendus est. Illum nemo tenetur ea.',
            'Incidunt rerum qui laboriosam sed omnis qui. Maiores magni repellat sit ut est nisi sit eos. Quia debitis dolorem repellendus tenetur. Quos vero necessitatibus est hic saepe architecto rerum quis.',
            'Omnis dolore molestias nam ut aut. Accusantium maiores nam ducimus unde veritatis voluptate autem aperiam. Iure pariatur iste aspernatur soluta sit nemo consequatur ipsam.',
            'Ut asperiores ipsum cumque incidunt qui. Dicta dicta omnis nesciunt magni odit. Porro non aut quibusdam labore eos. Neque molestiae sed animi velit.',
            'Dolor rerum aut quia earum nihil laboriosam iste non. Qui sit voluptatem non et. Eveniet tempora ut iste rerum ab aut sapiente.'
        ];

        // Pega a lista de posts cadastrados
        $postRep = $manager->getRepository('App:Post');
        $posts = $postRep->findAll();

        $userRep = $manager->getRepository('App:User');
        $users = $userRep->findAll();

        // Data e hora atuais
        $now = new \DateTime('NOW');

        foreach ($posts as $post) {
            foreach ($comments as $text) {
                $comment = new Comment();

                // Pega aleatoriamente um dos 
                // usuários administradores
                $user = $users[array_rand($users)];

                // Aleatoriamente aprova o comentário
                $approvedAt = rand(0, 1) > 0 ? $now : null;

                $comment->setText($text)
                    ->setUser($user)
                    ->setPost($post)
                    ->setCreatedAt($now)
                    ->setApprovedAt($approvedAt);

                $manager->persist($comment);
            }
        }

        $manager->flush();
    }
}
