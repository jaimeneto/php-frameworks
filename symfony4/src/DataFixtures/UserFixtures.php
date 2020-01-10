<?php
 
namespace App\DataFixtures;
 
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
 
class UserFixtures extends Fixture
{
  private $passwordEncoder;
 
  public function __construct(
    UserPasswordEncoderInterface $passwordEncoder)
  {
    $this->passwordEncoder = $passwordEncoder;
  }
 
  public function load(ObjectManager $manager)
  {
    $users = [
      [
        'Administrador', 
        'admin@email.com', 
        ['ROLE_ADMIN']
      ], [
        'User', 
        'user@email.com', 
        ['ROLE_USER']
      ], [
        'Fulano', 
        'fulano@email.com', 
        ['ROLE_ADMIN']
      ], [
        'Sicrano', 
        'sicrano@email.com', 
        ['ROLE_USER']
      ], [
        'Beltrano', 
        'beltrano@email.com', 
        ['ROLE_USER']
      ]
    ];
 
    // Data e hora atuais
    $now = new \DateTime('NOW'); 
 
    foreach($users as $k => $u) {
      $user = new User();
  
      $pw = $this->passwordEncoder
        ->encodePassword($user, 'symfony');
  
      $user->setName($u[0]);
      $user->setEmail($u[1]);
      $user->setRoles($u[2]);
      $user->setPassword($pw);
      $user->setCreatedAt($now);
      $user->setUpdatedAt($now);
  
      $manager->persist($user);
    }
 
    $manager->flush();
  }
}
