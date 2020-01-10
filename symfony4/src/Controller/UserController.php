<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Form\RegisterForm;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends Controller
{
  /** @var EntityManagerInterface */
  private $entityManager;

  /** @var TranslatorInterface */
  private $translator;

  /** @var \Doctrine\Common\Persistence\ObjectRepository */
  private $userRep;

  /** @var UserPasswordEncoderInterface */
  private $passwordEncoder;

  private $salt = '$2a$08$PHPFWsymfony$';

  /**
   * @param EntityManagerInterface $entityManager
   */
  public function __construct(
    EntityManagerInterface $entityManager,
    TranslatorInterface $translator,
    UserPasswordEncoderInterface $passwordEncoder
  ) {
    $this->entityManager = $entityManager;
    $this->translator = $translator;
    $this->passwordEncoder = $passwordEncoder;
    $this->userRep =
      $entityManager->getRepository('App:User');
  }


  /**
   * @Route("/register", name="user_register")
   */
  public function register(Request $request)
  {
    if ($this->getUser()) {
      return $this->redirectToRoute('homepage');
    }

    $user = new User();
    $form = $this->createForm(
      RegisterForm::class,
      $user
    );
    $form->handleRequest($request);

    // Verifica se é válido
    if ($form->isSubmitted() && $form->isValid()) {
      $now = new \DateTime('now');

      $password = $this->passwordEncoder
        ->encodePassword(
          $user,
          $form->get('password')->getData()
        );

      $user->setRoles(['ROLE_USER'])
        ->setCreatedAt($now)
        ->setUpdatedAt($now)
        ->setPassword($password);

      $this->entityManager->persist($user);
      $this->entityManager->flush($user);

      $this->addFlash(
        'success',
        $this->translator->trans(
          'User successfully registered'
        )
      );

      return $this->redirectToRoute(
        'user_sendVerificationMail',
        [
          'email' => $user->getEmail()
        ]
      );
    }

    return $this->render(
      'user/register.html.twig',
      ['form' => $form->createView()]
    );
  }

  /**
   * Envia uma mensagem com um link de validação 
   * para o e-mail do usuário
   *
   * @Route("sendVerificationMail/{email}", name="user_sendVerificationMail")
   */
  public function sendVerificationMail(
    string $email,
    \Swift_Mailer $mailer
  ) {
    $user = $this->userRep->findOneByEmail($email);

    if (!$user) {
      $this->addFlash('error', $this->translator
        ->trans('Invalid email!'));
      return $this->redirectToRoute(
        'user_register'
      );
    }

    $code = crypt($user->getEmail()
      . $user->getPassword(), $this->salt);

    $url = $this->generateUrl('user_verifyEmail', [
      'email' => $user->getEmail(),
      'code'  => $code
    ], UrlGeneratorInterface::ABSOLUTE_URL);

    try {
      $subject = 'PHP Frameworks: Symfony - Verificação de e-mail';
      $message = (new \Swift_Message($subject))
        ->setFrom('INSIRA_SEU_EMAIL_AQUI')
        ->setTo([
          $user->getEmail() => $user->getName()
        ])
        ->setBody('<p>Você se cadastrou com 
          sucesso. Clique no link abaixo para 
          validar o seu e-mail e ativar sua 
          conta:</p><a href="' . $url . '">Ativar 
          minha conta</a>');

      $result = $mailer->send($message);

      $this->addFlash(
        'success',
        $this->translator->trans(
          'A verification mail was sent to %email%',
          ['%email%' => $user->getEmail()]
        )
      );
    } catch (\Exception $e) {
      $this->addFlash('error', $e->getMessage());
      $this->addFlash(
        'error',
        $this->translator->trans(
          'Error trying to send verification mail'
        )
      );
    }

    return $this->redirectToRoute('app_login');
  }

  /**
   * Verifica se o código de validação de email 
   * está correto e define a data de ativação 
   * do email no usuário para ativar 
   * a conta
   *
   * @Route("/verifyEmail/{email}/{code}", name="user_verifyEmail")
   * @param string $email Endereço de e-mail
   * @param string $code Código de verificação
   */
  public function verifyEmail($email, $code)
  {
    $user = $this->userRep->findOneByEmail($email);

    if (!$user) {
      $this->addFlash('error', $this->translator
        ->trans('Invalid email!'));
      return $this->redirectToRoute(
        'user_register'
      );
    }

    $code2 = crypt($user->getEmail()
      . $user->getPassword(), $this->salt);

    if ($code !== $code2) {
      $this->addFlash(
        'error',
        $this->translator->trans(
          'Invalid validation code!'
        )
      );
    }

    try {
      $user->setEmailVerifiedAt(
        new \DateTime('now')
      );
      $this->entityManager->flush();

      $this->addFlash(
        'success',
        $this->translator->trans(
          'E-mail successfully verified. You can log in now.'
        )
      );
    } catch (\Exception $e) {
      $this->addFlash(
        'error',
        $this->translator->trans(
          'Error trying to restore user!'
        )
      );
    }

    // Volta para a lista de usuários cadastrados
    return $this->redirectToRoute('app_login');
  }

  /**
   * @Route("/admin/user", name="admin_user")
   * @Route("/admin/user/list/{page<\d+>}", name="admin_user_list")
   * @param int $page Página a ser exibida
   */
  public function list(
    int $page = 1,
    Request $request
  ) {
    $limit = 10;
    $users = $this->userRep->paginate($page, $limit);
    $total = $this->userRep->count([]);

    return $this->render('user/list.html.twig', [
      'users' => $users,
      'total' => $total,
      'page'  => $page,
      'limit' => $limit
    ]);
  }

  /**
   * Transforma um usuário comum em admin
   *
   * @Route("/admin/user/turnIntoAdmin/{userId}/{page<\d+>?1}", name="admin_user_turnIntoAdmin")
   * @param $userId
   * @param $page
   */
  public function turnIntoAdmin(int $userId, int $page)
  {
    $user = $this->userRep->find($userId);

    if (!$user) {
      $this->addFlash(
        'error',
        $this->translator->trans('Invalid user!')
      );
      return $this->redirectToRoute(
        'admin_user_list',
        ['page' => $page]
      );
    }

    try {
      $user->setRoles(['ROLE_ADMIN']);
      $this->entityManager->flush();

      $this->addFlash(
        'success',
        $this->translator->trans(
          'User successfully turned into admin.'
        )
      );
    } catch (\Exception $e) {
      $this->addFlash(
        'error',
        $this->translator->trans(
          'Error trying to turn user into admin!'
        )
      );
    }

    // Volta para a lista de usuários cadastrados
    return $this->redirectToRoute(
      'admin_user_list',
      ['page' => $page]
    );
  }

  /**
   * Manda um usuário para a lixeira(define a data 
   * e hora que foi excluído no campo deleted_at no 
   * banco de dados), ou, se o parâmetro $force for 
   * true, exclui definitivamente
   *
   * @Route("/admin/user/delete/{userId}", name="admin_user_delete", defaults={"force": false})
   * @Route("/admin/user/destroy/{userId}", name="admin_user_destroy", defaults={"force": true})
   * @param int $userId Id do usuário
   * @param int $page Página da lista de usuários a redirecionar
   * @param boolean $force false envia para a lixeira, true exclui do banco
   */
  public function delete(
    int $userId,
    int $page,
    $force
  ) {
    $user = $this->userRep->find($userId);

    if (!$user) {
      $this->addFlash(
        'error',
        $this->translator->trans('Invalid user!')
      );
      return $this->redirectToRoute(
        'admin_user_list',
        ['page' => $page]
      );
    }

    try {
      if ($force) {
        // Exclui o usuário do banco de dados
        $this->entityManager->remove($user);
        $msg = 'User successfully deleted.';
      } else {
        // Envia o usuário pra lixeira (deleted_at)
        $user->setDeletedAt(new \Datetime('now'));
        $msg = 'User successfully sent to trash.';
      }
      $this->entityManager->flush();
      $this->addFlash(
        'success',
        $this->translator->trans($msg)
      );
    } catch (\Exception $e) {
      $this->addFlash(
        'error',
        $this->translator->trans(
          'Error trying to delete user!'
        )
      );
    }

    // Volta para a lista de usuários cadastrados
    return $this->redirectToRoute(
      'admin_user_list',
      ['page' => $page]
    );
  }

  /**
   * Recupera um usuário da lixeira 
   * (anula o campo deleted_at)
   *
   * @Route("/admin/user/restore/{userId}", name="admin_user_restore")
   * @param int $userId Id do usuário
   * @param int $page Página da lista de usuários a redirecionar
   */
  public function restore(int $userId, int $page)
  {
    $user = $this->userRep->find($userId);

    if (!$user) {
      $this->addFlash(
        'error',
        $this->translator->trans('Invalid user!')
      );
      return $this->redirectToRoute(
        'admin_user_list',
        ['page' => $page]
      );
    }

    try {
      $user->setDeletedAt(null);
      $this->entityManager->flush();

      $this->addFlash(
        'success',
        $this->translator->trans(
          'User successfully restored.'
        )
      );
    } catch (\Exception $e) {
      $this->addFlash(
        'error',
        $this->translator->trans(
          'Error trying to restore user!'
        )
      );
    }

    // Volta para a lista de usuários cadastrados
    return $this->redirectToRoute(
      'admin_user_list',
      ['page' => $page]
    );
  }
}
