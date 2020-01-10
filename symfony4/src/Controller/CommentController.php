<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentForm;

class CommentController extends AbstractController
{
  /** @var EntityManagerInterface */
  private $entityManager;

  /** @var TranslatorInterface */
  private $translator;

  /** @var \Doctrine\Common\Persistence\ObjectRepository */
  private $commentRep;

  /**
   * @param EntityManagerInterface $entityManager
   */
  public function __construct(
    EntityManagerInterface $entityManager,
    TranslatorInterface $translator
  ) {
    $this->entityManager = $entityManager;
    $this->translator = $translator;
    $this->commentRep = $entityManager
      ->getRepository('App:Comment');
  }

  /**
   * @Route("/comment", name="comment")
   */
  public function index()
  {
    return $this->render('comment/index.html.twig', [
      'controller_name' => 'CommentController',
    ]);
  }

  /**
   * @Route("/admin/comment", name="admin_comment")
   * @Route("/admin/comment/list/{page<\d+>}", name="admin_comment_list")
   * @param int $page Página a ser exibida
   */
  public function list(int $page = 1, Request $request)
  {
    $limit = 10;
    $comments = $this->commentRep->paginate($page, $limit);
    $total = $this->commentRep->count([]);

    return $this->render('comment/list.html.twig', [
      'comments' => $comments,
      'total'    => $total,
      'page'     => $page,
      'limit'    => $limit
    ]);
  }

  /**
   * Aprova um comentário de um post
   *
   * @Route("/admin/comment/approve/{commentId}/{page<\d+>?1}/{postId<\d+>?}", name="admin_comment_approve")
   * @param int $commentId Id do comentário
   * @param int $page Página da lista de comentários a redirecionar
   * @param int $postId Id do post, indica que veio da página de exibição do post
   */
  public function approve(int $commentId, int $page, int $postId = null)
  {
    // Busca o comentário pelo id
    $comment = $this->commentRep->find($commentId);

    // Caso o id do comentário seja inválido volta 
    // para a lista de cadastrados e exibe uma 
    // mensagem de erro
    if (!$comment) {
      $this->addFlash('error', $this->translator
        ->trans('Invalid comment!'));
      return $postId ? $this->redirectToRoute(
        'post_show',
        [
          'id' => $postId,
          '_fragment' => 'comments'
        ]
      ) : $this->redirectToRoute(
        'admin_comment_list',
        ['page' => $page]
      );
    }

    try {
      // Define a data de aprovação do comentário
      $comment->setApprovedAt(new \DateTime('now'));
      $this->entityManager->persist($comment);
      $this->entityManager->flush();

      // Exibe uma mensagem de sucesso
      $this->addFlash('success', $this->translator
        ->trans('Comment successfully approved.'));
    } catch (\Exception $e) {
      // Se ocorrer qualquer problema exibe um 
      // erro para o usuário
      $this->addFlash(
        'error',
        $this->translator->trans(
          'Error trying to approve comment!'
        )
      );
    }

    // Volta para a lista de comments cadastrados
    return $postId
      ? $this->redirectToRoute('post_show', [
        'id' => $postId,
        '_fragment' => 'comments'
      ]) : $this->redirectToRoute(
        'admin_comment_list',
        ['page' => $page]
      );
  }

  /**
   * Exclui um comentário de um post
   *
   * @Route("/admin/comment/delete/{commentId}/{page<\d+>?1}/{postId<\d+>?}", name="admin_comment_delete")
   * @param int $commentId Id do comentário
   * @param int $page Página da lista de comentários a redirecionar
   * @param int $postId Id do post, indica que veio da página de exibição do post
   */
  public function delete(
    int $commentId,
    int $page,
    int $postId = null
  ) {
    // Busca o comentário pelo id
    $comment = $this->commentRep->find($commentId);

    // Caso o id do comentário seja inválido volta 
    // para a lista de cadastrados e exibe uma 
    // mensagem de erro
    if (!$comment) {
      $this->addFlash('error', $this->translator
        ->trans('Invalid comment!'));
      return $postId
        ? $this->redirectToRoute('post_show', [
          'id' => $postId,
          '_fragment' => 'comments'
        ]) : $this->redirectToRoute(
          'admin_comment_list',
          ['page' => $page]
        );
    }

    try {
      // Exclui o comentário do banco de dados
      $this->entityManager->remove($comment);
      $this->entityManager->flush();

      // Exibe uma mensagem de sucesso
      $this->addFlash(
        'success',
        $this->translator->trans(
          'Comment successfully deleted.'
        )
      );
    } catch (\Exception $e) {
      // Se ocorrer qualquer problema exibe 
      // um erro para o usuário
      $this->addFlash(
        'error',
        $this->translator->trans(
          'Error trying to delete comment!'
        )
      );
    }

    // Volta para a lista de comments cadastrados
    return $postId
      ? $this->redirectToRoute(
        'post_show',
        ['id' => $postId, '_fragment' => 'comments']
      )
      : $this->redirectToRoute(
        'admin_comment_list',
        ['page' => $page]
      );
  }

  /**
   * Cadastra um comentário
   * 
   * @Route("/comment/create/{id}", name="comment_create")
   */
  public function create(Post $post, Request $request)
  {
    // Cria o formulário
    $comment = new Comment();
    $form = $this->createForm(
      CommentForm::class,
      $comment
    );
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      try {
        // Define a datas de criação, post,
        // associa o autor e salva os dados
        $now = new \DateTime('now');
        $comment->setCreatedAt($now);
        $comment->setUser($this->getUser());
        $comment->setPost($post);

        // Se for admin já aprova automaticamente
        if ($this->isGranted('ROLE_ADMIN')) {
          $comment->setApprovedAt($now);
        }

        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        // Exibe uma mensagem de sucesso
        $this->addFlash(
          'success',
          $this->translator->trans(
            'Comment successfully created.'
          )
        );
      } catch (\Exception $e) {
        // Se ocorrer qualquer problema exibe um erro 
        $this->addFlash(
          'error',
          $this->translator->trans(
            'Error trying to create post!'
          )
        );
      }
    }

    return $this->redirectToRoute('post_show', [
      'id'        => $post->getId(),
      '_fragment' => 'comments'
    ]);
  }
}
