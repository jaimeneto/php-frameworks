<?php

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;


class UsersController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->tag->prependTitle('Usuários | ');

        $this->persistent->parameters = null;

        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, 'Users', $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery('page', 'int');
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = [];
        }
        $parameters['order'] = 'id';

        $users = Users::find($parameters);

        $paginator = new Paginator([
            'data'  => $users,
            'limit' => 10,
            'page'  => $numberPage
        ]);

        $this->view->page = $paginator->getPaginate();
    }


    /**
     * Displays the creation form
     */
    public function newAction()
    {
        $this->tag->prependTitle('Registrar-se | ');
    }

    /**
     * Edits a user
     *
     * @param string $id
     */
    public function editAction($id)
    {
        if (!$this->request->isPost()) {

            $user = Users::findFirstByid($id);
            if (!$user) {
                $this->flash->error("user was not found");

                $this->dispatcher->forward([
                    'controller' => "users",
                    'action' => 'index'
                ]);

                return;
            }

            $this->view->id = $user->getId();

            $this->tag->setDefault("id", $user->getId());
            $this->tag->setDefault("name", $user->getName());
            $this->tag->setDefault("email", $user->getEmail());
            $this->tag->setDefault("type", $user->getType());
            $this->tag->setDefault("password", $user->getPassword());
            $this->tag->setDefault("remember_token", $user->getRememberToken());
            $this->tag->setDefault("created_at", $user->getCreatedAt());
            $this->tag->setDefault("email_verified_at", $user->getEmailVerifiedAt());
            $this->tag->setDefault("updated_at", $user->getUpdatedAt());
            $this->tag->setDefault("accessed_at", $user->getAccessedAt());
            $this->tag->setDefault("deleted_at", $user->getDeletedAt());
        }
    }

    /**
     * Creates a new user
     */
    public function createAction()
    {
        // Se já estiver autenticado, redireciona para a página inicial
        if ($this->session->has('auth')) {
            return $this->response->redirect('');
        }

        // Verifica se a requisição foi enviada via POST
        // ou retorna para o formulário de cadastro
        if (!$this->request->isPost()) {
            return $this->response->redirect('register');
        }

        $password = $this->request->getPost("password");

        // Verifica se os campos de senha e confirmação conferem
        // ou volta para o formulário com uma mensagem de erro
        if ($password !== $this->request->getPost("password_confirmation")) {
            $this->flash->error("As senhas não conferem");

            $this->dispatcher->forward([
                'controller' => "users",
                'action' => 'new'
            ]);
            return;
        }

        $now = date('Y-m-d H:i:s');

        $user = new Users();
        $user->setName($this->request->getPost("name"));
        $user->setEmail($this->request->getPost("email", "email"));
        $user->setPassword($this->security->hash($password));

        // Se não conseguir salvar, adiciona as mensagens de erro
        // de validação e volta para o formulário
        if (!$user->save()) {
            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward([
                'controller' => "users",
                'action' => 'new'
            ]);
        }

        //@TODO Enviar o e-mail de verificação

        // Exibe uma mensagem de sucesso
        $this->flash->success("Usuário registrado com sucesso");

        // Redireciona para a tela de login
        $this->dispatcher->forward([
            'controller' => "auth",
            'action' => 'login'
        ]);
    }

    /**
     * Saves a user edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "users",
                'action' => 'index'
            ]);

            return;
        }

        $id = $this->request->getPost("id");
        $user = Users::findFirstByid($id);

        if (!$user) {
            $this->flash->error("user does not exist " . $id);

            $this->dispatcher->forward([
                'controller' => "users",
                'action' => 'index'
            ]);

            return;
        }

        $user->setname($this->request->getPost("name"));
        $user->setemail($this->request->getPost("email", "email"));
        $user->settype($this->request->getPost("type"));
        $user->setpassword($this->request->getPost("password"));
        $user->setrememberToken($this->request->getPost("remember_token"));
        $user->setcreatedAt($this->request->getPost("created_at"));
        $user->setemailVerifiedAt($this->request->getPost("email_verified_at"));
        $user->setupdatedAt($this->request->getPost("updated_at"));
        $user->setaccessedAt($this->request->getPost("accessed_at"));
        $user->setdeletedAt($this->request->getPost("deleted_at"));


        if (!$user->save()) {

            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "users",
                'action' => 'edit',
                'params' => [$user->getId()]
            ]);

            return;
        }

        $this->flash->success("user was updated successfully");

        $this->dispatcher->forward([
            'controller' => "users",
            'action' => 'index'
        ]);
    }

    /**
     * Deletes a user
     *
     * @param string $id
     */
    public function deleteAction($id, $force = false)
    {
        $user = Users::findFirstById($id);
        if (!$user) {
            $this->flash->error('Usuário não encontrado');

            return $this->dispatcher->forward([
                'controller' => 'users',
                'action' => 'index'
            ]);
        }

        // Define a data de exclusão do usuário, mas não
        // exclui permanentemente
        if (!$force) {
            $user->setDeletedAt(date('Y-m-d H:i:s'));

            if (!$user->save()) {

                foreach ($user->getMessages() as $message) {
                    $this->flash->error($message);
                }

                return $this->dispatcher->forward([
                    'controller' => "users",
                    'action' => 'index'
                ]);
            }

            // Exclui permanentemente o usuário
        } else if (!$user->delete()) {

            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward([
                'controller' => 'users',
                'action' => 'index'
            ]);
        }

        $this->flash->success('Usuário excluído com sucesso');

        $this->dispatcher->forward([
            'controller' => 'users',
            'action' => 'index'
        ]);
    }


    public function turnIntoAdminAction($id)
    {
        $user = Users::findFirstById($id);
        if (!$user) {
            $this->flash->error("Usuário não encontrado");

            return $this->dispatcher->forward([
                'controller' => "users",
                'action' => 'index'
            ]);
        }

        $user->setType('admin');
        $user->setUpdatedAt(date('Y-m-d H:i:s'));

        if (!$user->save()) {

            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward([
                'controller' => "users",
                'action' => 'index'
            ]);
        }

        $this->flash->success("Usuário transformado em administrador com sucesso");

        $this->dispatcher->forward([
            'controller' => "users",
            'action' => "index"
        ]);
    }

    public function restoreAction($id)
    {
        $user = Users::findFirstById($id);
        if (!$user) {
            $this->flash->error('Usuário não encontrado');

            return $this->dispatcher->forward([
                'controller' => 'users',
                'action' => 'index'
            ]);
        }

        $user->setDeletedAt(null);
        $user->setUpdatedAt(date('Y-m-d H:i:s'));

        if (!$user->save()) {

            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward([
                'controller' => 'users',
                'action' => 'index'
            ]);
        }

        $this->flash->success('Usuário restaurado com sucesso');

        $this->dispatcher->forward([
            'controller' => 'users',
            'action' => 'index'
        ]);
    }
}
