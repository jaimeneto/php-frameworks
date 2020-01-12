<?php

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;


class PostsController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->tag->prependTitle('Posts | ');

        $this->persistent->parameters = null;

        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, 'Posts', $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery('page', 'int');
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = [];
        }
        $parameters['order'] = 'id DESC';
        $posts = Posts::find($parameters);

        $paginator = new Paginator([
            'data' => $posts,
            'limit' => 10,
            'page' => $numberPage
        ]);

        $users = Users::findByType('admin');

        $this->view->users = $users;
        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Displays the creation form
     */
    public function newAction()
    {
        $this->tag->prependTitle('Cadastrar Post | ');
    }

    /**
     * Edits a post
     *
     * @param string $id
     */
    public function editAction($id)
    {
        if (!$this->request->isPost()) {

            $post = Posts::findFirstById($id);
            if (!$post) {
                $this->flash->error('Post não encontrado');

                return $this->dispatcher->forward([
                    'controller' => 'posts',
                    'action' => 'index'
                ]);
            }

            $this->tag->prependTitle('Alterar Post | ');

            $this->view->id = $post->getId();

            $this->tag->setDefault('id', $post->getId());
            $this->tag->setDefault('title', $post->getTitle());
            $this->tag->setDefault('text', $post->getText());
        }
    }


    /**
     * Creates a new post
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => 'posts',
                'action' => 'index'
            ]);
            return;
        }

        $post = new Posts();
        $post->setTitle($this->request->getPost("title"));
        $post->setText($this->request->getPost("text"));
        $post->setUserId($this->session->get('auth')->id);

        if (!$post->save()) {
            foreach ($post->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => 'posts',
                'action' => 'new'
            ]);
            return;
        }

        $this->flash->success('Post criado com sucesso');

        // Limpa o post para não preencher o formulário de filtro
        $_POST = [];

        $this->dispatcher->forward([
            'controller' => 'posts',
            'action' => 'index'
        ]);
    }


    /**
     * Saves a post edited
     *
     */
    public function saveAction()
    {
        if (!$this->request->isPost()) {
            return $this->dispatcher->forward([
                'controller' => 'posts',
                'action' => 'index'
            ]);
        }

        $id = $this->request->getPost('id');
        $post = Posts::findFirstByid($id);

        if (!$post) {
            $this->flash->error('Post não encontrado: ' . $id);

            return $this->dispatcher->forward([
                'controller' => 'posts',
                'action' => 'index'
            ]);
        }

        $post->setTitle($this->request->getPost('title'));
        $post->setText($this->request->getPost('text'));

        if (!$post->save()) {
            foreach ($post->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward([
                'controller' => 'posts',
                'action' => 'edit',
                'params' => [$post->getId()]
            ]);
        }

        $this->flash->success('Post alterado com sucesso');

        // Limpa o post para não preencher o formulário de filtro
        $_POST = [];
        
        $this->dispatcher->forward([
            'controller' => 'posts',
            'action' => 'index'
        ]);
    }

    /**
     * Deletes a post
     *
     * @param string $id
     */
    public function deleteAction($id)
    {
        $post = Posts::findFirstById($id);
        if (!$post) {
            $this->flash->error('Post não encontrado');

            return $this->dispatcher->forward([
                'controller' => 'posts',
                'action' => 'index'
            ]);
        }

        if (!$post->delete()) {
            foreach ($post->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward([
                'controller' => 'posts',
                'action' => 'index'
            ]);
        }

        $this->flash->success('Post excluído com sucesso');

        $this->dispatcher->forward([
            'controller' => 'posts',
            'action' => "index"
        ]);
    }
}
