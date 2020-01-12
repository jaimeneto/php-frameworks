<?php

namespace Blog\Controller;

use Blog\Model\PostTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Blog\Form\PostForm;
use Blog\Model\Post;
use Blog\Form\CommentForm;

class PostController extends AbstractActionController
{
    private $table;

    public function __construct(PostTable $table)
    {
        $this->table = $table;
    }

    public function indexAction()
    {
        // Verifica se o usuário tem acesso a esta tela e, caso não tenha,
        // redireciona para a tela de erro de autorização
        if (!$this->access('blog.post.manage')) {
            return $this->redirect()->toRoute('not-authorized');
        }

        // Pega o número da página
        $page = (int)$this->params()->fromQuery('page', 1);
        $page = ($page < 1) ? 1 : $page;

        // Carrega os posts cadastrados paginados com 10 posts por página
        // e ordenados pelo mais recente primeiro,
        $paginator = $this->table->paginate($page, 10, 'posts.id DESC');

        return new ViewModel(['posts' => $paginator]);
    }

    public function listAction()
    {
        if (!$this->access('blog.post.list')) {
            return $this->redirect()->toRoute('not-authorized');
        }

        // Pega o número da página
        $page = (int)$this->params()->fromQuery('page', 1);
        $page = ($page < 1) ? 1 : $page;

        // Carrega os posts cadastrados paginados com 10 posts por página
        // e ordenados pelo mais recente primeiro,
        $paginator = $this->table->paginate($page, 10, 'posts.id DESC');

        return new ViewModel(['posts' => $paginator]);
    }

    public function addAction()
    {
        if (!$this->access('blog.post.add')) {
            return $this->redirect()->toRoute('not-authorized');
        }

        $request = $this->getRequest();

        // Se clicar no botão cancelar volta para a tela
        // com a lista de posts cadastrados
        if ($request->getPost('cancel')) {
            return $this->redirect()->toRoute('post');
        }

        $form = new PostForm();

        // Altera o label do botão submit
        $form->get('submit')->setValue('Cadastrar');

        // Se não foi submetido, exibe o formulário
        if (!$request->isPost()) {
            return ['form' => $form];
        }

        // Insere os filtros de Post no formulário
        $post = new Post();
        $form->setInputFilter($post->getInputFilter());

        // Insere os dados submetidos ao formulário
        $form->setData($request->getPost());

        // Se os dados não forem válidos retorna ao formulário
        if (!$form->isValid()) {
            if (isset($form->getMessages('csrf')['isEmpty'])) {
                return $this->redirect()->toRoute('not-authorized');
            }

            $this->flashMessenger()->addErrorMessage('Dados inválidos');
            return ['form' => $form];
        }

        // Pega os dados do formulário, define o user_id
        // com o id do usuário logado e salva no banco
        $data = $form->getData();
        $data['user_id'] = 1;

        // Pega os dados do formulário
        $data = $form->getData();

        // Pega o id do usuário logado
        $data['user_id'] = $this->auth()->id;

        // Salva o novo registro no banco
        try {
            $post->exchangeArray($data);
            $this->table->save($post);
            $this->flashMessenger()->addSuccessMessage('Post cadastrado com sucesso');
        } catch (\Exception $e) {
            $this->flashMessenger()->addErrorMessage('Arro ao tentar cadastrar post');
        }

        // Volta para a tela com a lista de posts cadastrados
        return $this->redirect()->toRoute('post');
    }

    public function editAction()
    {
        if (!$this->access('blog.post.edit')) {
            return $this->redirect()->toRoute('not-authorized');
        }

        $request = $this->getRequest();

        // Se clicar no botão cancelar volta para a tela
        // com a lista de posts cadastrados
        if ($request->getPost('cancel')) {
            return $this->redirect()->toRoute('post');
        }

        $id = (int)$this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('post', ['action' => 'add']);
        }

        try {
            $post = $this->table->find($id);
        } catch (\Exception $e) {
            // Se não encontrar o post, volta para a tela com a lista de posts
            $this->flashMessenger()->addErrorMessage('Post inválido');
            return $this->redirect()->toRoute('post', ['action' => 'index']);
        }

        $form = new PostForm();
        $form->bind($post);
        $form->get('submit')->setAttribute('value', 'Alterar');

        $viewData = ['id' => $id, 'form' => $form];

        if (!$request->isPost()) {
            return $viewData;
        }

        $form->setInputFilter($post->getInputFilter());
        $form->setData($request->getPost());

        if (!$form->isValid()) {
            if (isset($form->getMessages('csrf')['isEmpty'])) {
                return $this->redirect()->toRoute('not-authorized');
            }
                        
            $this->flashMessenger()->addErrorMessage('Dados inválidos');
            return $viewData;
        }

        try {
            $this->table->save($post);
            $this->flashMessenger()->addSuccessMessage('Post alterado com sucesso');
        } catch (\Exception $e) {
            $this->flashMessenger()->addErrorMessage('Erro ao tentar alterar post');
        }

        // Volta para a tela com a lista de posts cadastrados
        return $this->redirect()->toRoute('post', ['action' => 'index']);
    }


    public function deleteAction()
    {
        if (!$this->access('blog.post.delete')) {
            return $this->redirect()->toRoute('not-authorized');
        }

        $id = (int)$this->params()->fromRoute('id', 0);

        if (0 === $id) {
            $this->flashMessenger()->addErrorMessage('Post inválido');
            return $this->redirect()->toRoute('post');
        }

        try {
            $this->table->delete($id);
            $this->flashMessenger()->addSuccessMessage('Post excluído com sucesso');
        } catch (\Exception $e) {
            $this->flashMessenger()->addErrorMessage('Erro ao tentar excluir o post');
        }

        // Volta para a tela com a lista de posts cadastrados
        return $this->redirect()->toRoute('post');
    }


    public function viewAction()
    {
        if (!$this->access('blog.post.view')) {
            return $this->redirect()->toRoute('not-authorized');
        }

        $id = (int)$this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('home');
        }

        try {
            $post = $this->table->find($id);
            $user = new \StdClass();
            $user->name = 'Administrator';
        } catch (\Exception $e) {
            // Se não encontrar o post, volta para a tela inicial
            return $this->redirect()->toRoute('home');
        }

        $commentForm = new CommentForm();
        $commentForm->get('post_id')->setValue($id);

        return new ViewModel([
            'post' => $post,
            'user' => $user,
            'form' => $commentForm
        ]);
    }
}
