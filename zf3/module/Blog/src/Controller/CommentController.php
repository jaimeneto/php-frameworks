<?php

namespace Blog\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Blog\Model\CommentTable;
use Blog\Model\Comment;
use Blog\Form\CommentForm;

class CommentController extends AbstractActionController
{
    private $table;

    public function __construct(CommentTable $table)
    {
        $this->table = $table;
    }

    public function indexAction()
    {
        if (!$this->access('blog.comment.manage')) {
            return $this->redirect()->toRoute('not-authorized');
        }

        // Pega o número da página
        $page = (int)$this->params()->fromQuery('page', 1);
        $page = ($page < 1) ? 1 : $page;

        // Carrega os comentários cadastrados paginados com 10 por página
        // e ordenados pelo mais recente primeiro,
        $paginator = $this->table->paginate($page, 10, 'comments.id DESC');

        return new ViewModel(['comments' => $paginator]);
    }

    public function listAction()
    { }

    public function addAction()
    {
        if (!$this->access('blog.comment.add')) {
            return $this->redirect()->toRoute('not-authorized');
        }

        $request = $this->getRequest();

        // Se não for uma requisição POST ou
        // se não tiver o id do post volta para a tela inicial
        if (!$request->isPost() || !$request->getPost('post_id')) {
            return $this->redirect()->toRoute('home');
        }

        $form = new CommentForm();

        $postId = $request->getPost('post_id');

        // Insere os filtros de Comment no formulário
        $comment = new Comment();
        $form->setInputFilter($comment->getInputFilter());

        // Insere os dados submetidos ao formulário
        $data = $request->getPost();
        $data['id'] = 0;
        $form->setData($data);

        // Se os dados não forem válidos retorna ao formulário
        if (!$form->isValid()) {
            if (isset($form->getMessages('csrf')['isEmpty'])) {
                return $this->redirect()->toRoute('not-authorized');
            }
     
            $this->flashMessenger()->addErrorMessage('Dados inválidos');
            return $this->redirect()->toRoute('viewPost', ['id' => $postId]);
        }

        // Pega os dados do formulário
        $data = $form->getData();

        // Pega o id do usuário autenticado
        $data['user_id'] = $this->auth()->id;

        // Salva o novo registro no banco
        try {
            $comment->exchangeArray($data);
            $this->table->save($comment);
            $this->flashMessenger()
                ->addSuccessMessage('Comentário cadastrado com sucesso');
        } catch (\Exception $e) {
            $this->flashMessenger()
                ->addErrorMessage('Erro ao tentar cadastrar o comentário');
        }

        // Volta para a tela do post
        return $this->redirect()->toRoute('viewPost', ['id' => $postId]);
    }

    public function approveAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        $postId = (int)$this->params()->fromQuery('post_id');

        if (0 === $id) {
            $this->flashMessenger()->addErrorMessage('Comentário inválido');
            return $postId
                ? $this->redirect()->toRoute('viewPost', ['id' => $postId])
                : $this->redirect()->toRoute('comment');
        }

        // Verifica se o usuário tem permissão para aprovar este comentário
        $comment = $this->table->find($id);
        if (!$this->access('blog.comment.approve', ['comment' => $comment])) {
            return $this->redirect()->toRoute('not-authorized');
        }

        try {
            $this->table->approve($id);
            $this->flashMessenger()->addSuccessMessage('Comentário aprovado com sucesso');
        } catch (\Exception $e) {
            // Se não encontrar o comentário
            $this->flashMessenger()->addErrorMessage($e->getMessage());
        }

        // Volta para a tela com a lista de comentários cadastrados
        // ou para a tela do post
        return $postId
            ? $this->redirect()->toRoute('viewPost', ['id' => $postId])
            : $this->redirect()->toRoute('comment');
    }

    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        $postId = (int)$this->params()->fromQuery('post_id');

        if (0 === $id) {
            $this->flashMessenger()->addErrorMessage('Comentário inválido');
            return $postId
                ? $this->redirect()->toRoute('viewPost', ['id' => $postId])
                : $this->redirect()->toRoute('comment');
        }

        // Verifica se o usuário tem permissão para excluir este comentário
        $comment = $this->table->find($id);
        if (!$this->access('blog.comment.delete', ['comment' => $comment])) {
            return $this->redirect()->toRoute('not-authorized');
        }

        try {
            $this->table->delete($id);
            $this->flashMessenger()
                ->addSuccessMessage('Comentário excluído com sucesso');
        } catch (\Exception $e) {
            $this->flashMessenger()
                ->addErrorMessage('Erro ao tentar excluir o comentário');
        }

        // Volta para a tela com a lista de comentários cadastrados
        // ou para a tela do post
        return $postId
            ? $this->redirect()->toRoute('viewPost', ['id' => $postId])
            : $this->redirect()->toRoute('comment');
    }
}
