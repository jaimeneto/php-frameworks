<?php

namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use User\Model\UserTable;
use User\Model\User;
use User\Form\RegisterForm;
use Zend\Crypt\Password\Bcrypt;
use User\Form\SendVerificationEmailForm;
use User\Form\ResetPasswordForm;

class RegisterController extends AbstractActionController
{
    private $table;
    private $mail;

    public function __construct(UserTable $table, $mail)
    {
        $this->table = $table;
        $this->mail = $mail;
    }

    public function indexAction()
    {
        // Verifica se o usuário tem acesso a esta tela e, caso não tenha,
        // redireciona para a tela de erro de autorização
        if (!$this->access('user.register')) {
            return $this->redirect()->toRoute('not-authorized');
        }

        $request = $this->getRequest();

        // Se clicar no botão cancelar volta para a tela de login
        if ($request->getPost('cancel')) {
            return $this->redirect()->toRoute('login');
        }

        $form = new RegisterForm();

        // Se não foi submetido, exibe o formulário
        if (!$request->isPost()) {
            return ['form' => $form];
        }

        // Insere os filtros de Post no formulário
        $user = new User();
        $form->setInputFilter($user->getInputFilter());

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

        // Pega os dados do formulário
        $data = $form->getData();

        $bcrypt = new Bcrypt();
        $data['password'] = $bcrypt->create($data['password']);

        // Salva o novo registro no banco
        try {
            $user->exchangeArray($data);
            $this->table->save($user);

            $this->flashMessenger()->addSuccessMessage(
                'Usuário cadastrado com sucesso'
            );
        } catch (\Exception $e) {
            $this->flashMessenger()->addErrorMessage(
                'Erro ao tentar cadastrar usuário'
            );
            return $this->redirect()->toRoute('register');
        }

        // Envia o e-mail de verificação
        try {
            $this->mail->sendVerificationMail($user);

            $this->flashMessenger()->addSuccessMessage(
                'Verifique seu e-mail para validar sua conta.'
            );
        } catch (\Exception $e) {
            $this->flashMessenger()->addErrorMessage(
                'Erro ao tentar enviar e-mail de verificação'
            );
        }

        // Volta para a tela de login
        return $this->redirect()->toRoute('login');
    }

    public function sendVerificationEmailAction()
    {
        if (!$this->access('user.sendVerificationEmail')) {
            return $this->redirect()->toRoute('not-authorized');
        }

        $request = $this->getRequest();

        // Se clicar no botão cancelar volta para a tela de login
        if ($request->getPost('cancel')) {
            return $this->redirect()->toRoute('login');
        }

        $form = new SendVerificationEmailForm();

        // Se não foi submetido, exibe o formulário
        if (!$request->isPost()) {
            return ['form' => $form];
        }

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

        // Pega os dados do formulário
        $data = $form->getData();

        try {
            $user = $this->table->findByEmail($data['email']);
        } catch (\Exception $e) {
            // Se não encontrar o usuário, volta para a tela com a lista de usuários
            $this->flashMessenger()->addErrorMessage('Usuário inválido');
            return $this->redirect()->toRoute('login');
        }

        // Envia o e-mail de verificação
        try {
            $this->mail->sendVerificationMail($user);

            $this->flashMessenger()->addSuccessMessage('E-mail de verificação enviado com sucesso');
            return $this->redirect()->toRoute('login');
        } catch (\Exception $e) {
            $this->flashMessenger()->addErrorMessage('Erro ao tentar enviar e-mail de verificação');
        }

        // Exibe o formulário
        return ['form' => $form];
    }

    public function verifyEmailAction()
    {
        if (!$this->access('user.verifyEmail')) {
            return $this->redirect()->toRoute('not-authorized');
        }

        $email = $this->params()->fromRoute('email', 0);
        $code = $this->params()->fromQuery('code', 0);

        if (0 === $email) {
            $this->flashMessenger()->addErrorMessage('Usuário inválido');
            return $this->redirect()->toRoute('sendVerificationEmail');
        }

        try {
            // Valida o e-mail e salva a data da verificação
            if ($this->table->verifyEmail($email, $code)) {
                $this->flashMessenger()->addSuccessMessage('E-mail verificado com sucesso');
                return $this->redirect()->toRoute('login');
            }
        } catch (\Exception $e) {
            // Se não encontrar o usuário, volta para a tela com a lista de usuários
            $this->flashMessenger()->addErrorMessage($e->getMessage());
        }

        // Volta para a tela inicial
        return $this->redirect()->toRoute('home');
    }

    public function resetPasswordAction()
    {
        if (!$this->access('user.resetPassword')) {
            return $this->redirect()->toRoute('not-authorized');
        }

        $request = $this->getRequest();

        // Se clicar no botão cancelar volta para a tela de login
        if ($request->getPost('cancel')) {
            return $this->redirect()->toRoute('login');
        }

        $form = new ResetPasswordForm();

        // Se não foi submetido, exibe o formulário
        if (!$request->isPost()) {
            return ['form' => $form];
        }

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

        // Pega os dados do formulário
        $data = $form->getData();

        try {
            $user = $this->table->findByEmail($data['email']);
        } catch (\Exception $e) {
            // Se não encontrar o usuário, volta para o formulário
            $this->flashMessenger()->addErrorMessage('Usuário inválido');
            return ['form' => $form];
        }

        // Envia o e-mail de verificação
        try {
            $this->mail->sendResetPasswordMail($user, $data['password']);

            $this->flashMessenger()->addSuccessMessage(
                'Requisição para redefinição de senha feita com sucesso. ' . 
                'Clique no link enviado para seu e-mail para confirmar ' . 
                'esta alteração');
            return $this->redirect()->toRoute('login');
        } catch (\Exception $e) {
            $this->flashMessenger()
                ->addErrorMessage('Erro ao tentar redefinir senha');
        }

        // Exibe o formulário
        return ['form' => $form];
    }

    public function confirmResetPasswordAction()
    {
        if (!$this->access('user.resetPassword')) {
            return $this->redirect()->toRoute('not-authorized');
        }

        $email = $this->params()->fromRoute('email', 0);
        $code = $this->params()->fromQuery('code', 0);

        if (0 === $email) {
            $this->flashMessenger()->addErrorMessage('Usuário inválido');
            return $this->redirect()->toRoute('login');
        }

        try {
            // Altera a senha
            if ($this->table->resetPassword($email, $code)) {
                $this->flashMessenger()
                    ->addSuccessMessage('Senha alterada com sucesso');
            }
        } catch (\Exception $e) {
            $this->flashMessenger()->addErrorMessage($e->getMessage());
        }

        // Volta para a tela de login
        return $this->redirect()->toRoute('login');
    }

}
