<?php

class AuthController extends ControllerBase
{

    public function loginAction($redirectTo = '')
    {
        // Se já estiver autenticado, redireciona para a página inicial
        if ($this->session->has('auth')) {
            return $this->response->redirect('');
        }

        // Verifica se já existe o cookie "Lembre-se de mim"
        if ($this->cookies->has('remember-me')) {
            $rememberMeCookie = $this->cookies->get('remember-me');
            $value = $rememberMeCookie->getValue();
        }

        $this->tag->prependTitle('Login | ');

        // Verifica se os dados foram enviados pelo formulário via POST
        // e valida o token de segurança para evitar ataques do tipo
        // Cross-Site Request Forgery (CSRF)
        if ($this->request->isPost() && $this->security->checkToken()) {
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');

            // Consulta o usuário pelo e-mail
            $user = Users::findFirstByEmail($email);

            // Verifica se a senha é válida
            if (
                $user &&
                $this->security->checkHash($password, $user->getPassword())
            ) {
                // Salva os dados na sessão
                $this->session->set('auth', (object)[
                    'id'    => $user->getId(),
                    'name'  => $user->getName(),
                    'email' => $user->getEmail(),
                    'type'  => $user->getType()
                ]);

                // Atualiza a data do último acesso
                $user->setAccessedAt(date('Y-m-d H:i:s'));
                
                $user->save();

                $this->flash->success('Bem vindo ' . $user->getName());

                // Cria o cookie "Lembre-se de mim"
                if ($this->request->getPost('remember')) {
                    $this->cookies->set(
                        'remember-me',
                        $email,
                        time() + 15 * 86400  // 15 dias
                    );
                    $this->cookies->send();
                }

                // Redireciona para a página que tentou acessar
                // ou para a tela inicial
                return $this->response->redirect($redirectTo);
            }

            $this->flash->error('E-mail ou senha inválidos');
        }

        $this->view->redirectTo = $redirectTo;
    }

    public function logoutAction()
    {
        // Exclui o cookie do "Lembre-se de mim"
        $rememberMeCookie = $this->cookies->get('remember-me');
        $rememberMeCookie->delete();

        // Limpa os dados da sessão
        $this->session->remove('auth');

        // Volta para a tela inicial
        return $this->dispatcher->forward([
            'controller' => 'index',
            'action'     => 'index',
        ]);
    }
}
