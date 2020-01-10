<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Post;
use App\Form\PostForm;
use App\Form\CommentForm;

class PostController extends AbstractController
{
  /** @var EntityManagerInterface */
  private $entityManager;

  /** @var TranslatorInterface */
  private $translator;

  /** @var \Doctrine\Common\Persistence\ObjectRepository */
  private $postRep;

  /**
   * @param EntityManagerInterface $entityManager
   */
  public function __construct(
    EntityManagerInterface $entityManager,
    TranslatorInterface $translator
  ) {
    $this->entityManager = $entityManager;
    $this->translator = $translator;
    $this->postRep = $entityManager
      ->getRepository('App:Post');
  }

  /**
   * @Route("/posts/{page<\d+>}", name="posts")
   */
  public function index(int $page = 1)
  {
    $limit = 10;
    $posts = $this->postRep->paginate($page, $limit);
    $total = $this->postRep->count([]);

    return $this->render('post/index.html.twig', [
      'posts' => $posts,
      'total' => $total,
      'page'  => $page,
      'limit' => $limit
    ]);
  }

  /**
   * @Route("/admin/post", name="admin_post")
   * @Route("/admin/post/list/{page<\d+>}", name="admin_post_list")
   * @param int $page Página a ser exibida
   */
  public function list(int $page = 1, Request $request)
  {
    $limit = 10;
    $posts = $this->postRep->paginate($page, $limit);
    $total = $this->postRep->count([]);

    return $this->render('post/list.html.twig', [
      'posts' => $posts,
      'total' => $total,
      'page'  => $page,
      'limit' => $limit
    ]);
  }

  /**
   * Cadastra um post
   * 
   * @Route("/admin/post/create", name="admin_post_create")
   */
  public function create(Request $request)
  {
    // Cria o formulário
    $post = new Post();
    $form = $this->createForm(
      PostForm::class,
      $post
    );
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      try {
        // Define as datas de criação e da 
        // última alteração, 
        // associa o autor e salva os dados do post
        $now = new \DateTime('now');
        $post->setCreatedAt($now);
        $post->setUpdatedAt($now);
        $post->setUser($this->getUser());
        $this->entityManager->persist($post);
        $this->entityManager->flush();

        // Exibe uma mensagem de sucesso
        $this->addFlash(
          'success',
          $this->translator->trans(
            'Post successfully created.'
          )
        );

        // Volta para a lista de posts cadastrados
        return $this->redirectToRoute(
          'admin_post_list'
        );
      } catch (\Exception $e) {
        // Se ocorrer qualquer problema exibe 
        // um erro 
        $this->addFlash(
          'error',
          $this->translator->trans(
            'Error trying to create post!'
          )
        );
      }
    }

    return $this->render('post/form.html.twig', [
      'pageTitle' =>
      $this->translator->trans('Create Post'),
      'form'      => $form->createView()
    ]);
  }

  /**
   * Altera um post
   *
   * @Route("/admin/post/edit/{postId}/{page<\d+>?1}", name="admin_post_edit")
   * @param int $postId Id do post
   * @param int $page Página da lista de posts a redirecionar
   */
  public function edit(
    int $postId,
    int $page,
    Request $request
  ) {
    // Busca o post pelo id
    $post = $this->postRep->find($postId);

    // Caso o id do post seja inválido volta 
    // para a lista de cadastrados e exibe uma 
    // mensagem de erro
    if (!$post) {
      $this->addFlash(
        'error',
        $this->translator->trans('Invalid post!')
      );

      return $this->redirectToRoute(
        'admin_post_list',
        ['page' => $page]
      );
    }

    // Cria o formulário
    $form = $this->createForm(
      PostForm::class,
      $post
    );
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      try {
        // Atualiza a data da última alteração e 
        //salva os dados do post
        $post->setUpdatedAt(new \DateTime('now'));
        $this->entityManager->persist($post);
        $this->entityManager->flush();

        // Exibe uma mensagem de sucesso
        $this->addFlash(
          'success',
          $this->translator->trans(
            'Post successfully updated.'
          )
        );

        // Volta para a lista de posts cadastrados
        return $this->redirectToRoute(
          'admin_post_list',
          ['page' => $page]
        );
      } catch (\Exception $e) {
        // Se ocorrer qualquer problema 
        // exibe um erro
        $this->addFlash(
          'error',
          $this->translator->trans(
            'Error trying to update post!'
          )
        );
      }
    }

    return $this->render('post/form.html.twig', [
      'pageTitle' => $this->translator
        ->trans('Edit Post'),
      'form'      => $form->createView(),
      'page'      => $page
    ]);
  }

  /**
   * Exclui um post
   *
   * @Route("/admin/post/delete/{postId}/{page<\d+>?1}", name="admin_post_delete")
   * @param int $postId Id do post
   * @param int $page Página da lista de posts a redirecionar
   */
  public function delete(int $postId, int $page)
  {
    // Busca o post pelo id
    $post = $this->postRep->find($postId);

    // Caso o id do post seja inválido volta para 
    // a lista de cadastrados e exibe uma mensagem 
    // de erro
    if (!$post) {
      $this->addFlash(
        'error',
        $this->translator->trans('Invalid post!')
      );
      return $this->redirectToRoute(
        'admin_post_list',
        ['page' => $page]
      );
    }

    try {
      // Exclui o post do banco de dados
      $this->entityManager->remove($post);
      $this->entityManager->flush();

      // Exibe uma mensagem de sucesso
      $this->addFlash(
        'success',
        $this->translator->trans(
          'Post successfully deleted.'
        )
      );
    } catch (\Exception $e) {
      // Se ocorrer qualquer problema exibe um 
      // erro para o usuário
      $this->addFlash(
        'error',
        $this->translator->trans(
          'Error trying to delete post!'
        )
      );
    }

    // Volta para a lista de posts cadastrados
    return $this->redirectToRoute(
      'admin_post_list',
      ['page' => $page]
    );
  }

  /**
   * @Route("post/{id}", name="post_show")
   */
  public function show(Post $post)
  {
    $form = $this->createForm(CommentForm::class);

    return $this->render('post/show.html.twig', [
      'post'        => $post,
      'commentForm' => $form->createView()
    ]);
  }
}
