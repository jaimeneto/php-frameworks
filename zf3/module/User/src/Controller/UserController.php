<?php

namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use User\Model\UserTable;

class UserController extends AbstractActionController
{
    private $table;

    public function __construct(UserTable $table)
    {
        $this->table = $table;
    }

    public function indexAction()
    {
        if (!$this->access('user.manage')) {
            return $this->redirect()->toRoute('not-authorized');
        }

        // Pega o número da página
        $page = (int)$this->params()->fromQuery('page', 1);
        $page = ($page < 1) ? 1 : $page;

        // Carrega os usuários cadastrados paginados com 10 usuários por
        // página e ordenados pelo mais recente primeiro
        $paginator = $this->table->paginate($page, 10);

        return new ViewModel(['users' => $paginator]);
    }

    public function deleteAction()
    {
        if (!$this->access('user.delete')) {
            return $this->redirect()->toRoute('not-authorized');
        }

        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            $this->flashMessenger()->addErrorMessage('Usuário inválido');
            return $this->redirect()->toRoute('user');
        }

        try {
            $this->table->delete($id);
            $this->flashMessenger()->addSuccessMessage('Usuário excluído com sucesso');
        } catch (\Exception $e) {
            $this->flashMessenger()->addErrorMessage('Erro ao tentar excluir o usuário');
        }

        // Volta para a tela com a lista de usuários cadastrados
        return $this->redirect()->toRoute('user');
    }

    public function restoreAction()
    {
        if (!$this->access('user.restore')) {
            return $this->redirect()->toRoute('not-authorized');
        }

        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            $this->flashMessenger()->addErrorMessage('Usuário inválido');
            return $this->redirect()->toRoute('user');
        }

        try {
            $this->table->restore($id);
            $this->flashMessenger()->addSuccessMessage('Usuário restaurado com sucesso');
        } catch (\Exception $e) {
            $this->flashMessenger()->addErrorMessage('Erro ao tentar restaurar o usuário');
        }

        // Volta para a tela com a lista de usuários cadastrados
        return $this->redirect()->toRoute('user');
    }

    public function turnIntoAdminAction()
    {
        if (!$this->access('user.turnintoAdmin')) {
            return $this->redirect()->toRoute('not-authorized');
        }

        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            $this->flashMessenger()->addErrorMessage('Usuário inválido');
            return $this->redirect()->toRoute('user');
        }

        try {
            $user = $this->table->find($id);
        } catch (\Exception $e) {
            // Se não encontrar o usuário, volta para a tela com a lista de usuários
            $this->flashMessenger()->addErrorMessage('Usuário inválido');
            return $this->redirect()->toRoute('user');
        }

        try {
            $user->type = 'admin';
            $this->table->save($user);
            $this->flashMessenger()
                ->addSuccessMessage('Usuário transformado em admin com sucesso');
        } catch (\Exception $e) {
            $this->flashMessenger()
                ->addErrorMessage('Erro ao tentar transformar usuário em admin');
        }

        // Volta para a tela com a lista de usuários cadastrados
        return $this->redirect()->toRoute('user');
    }

}
