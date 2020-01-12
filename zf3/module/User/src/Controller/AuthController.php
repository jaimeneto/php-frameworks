<?php

namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Authentication\Result;
use Zend\Uri\Uri;
use User\Form\LoginForm;

class AuthController extends AbstractActionController
{
    private $authService;

    public function __construct($authService)
    {
        $this->authService = $authService;
    }

    public function loginAction()
    {
        $redirectUrl = (string) $this->params()->fromQuery('redirectUrl', '');
        $url = $redirectUrl ?: $this->url()->fromRoute('home');

        if ($this->authService->hasIdentity()) {
            return $this->redirect()->toUrl($url);
        }

        // Cria o formulário de login
        $form = new LoginForm();
        $form->get('redirect_url')->setValue($redirectUrl);

        // Verifica se o formulário foi submetido
        if ($this->getRequest()->isPost()) {

            // Preenche o formulário com os dados submetidos
            $data = $this->params()->fromPost();
            $form->setData($data);

            // Valida o formulário
            if ($form->isValid()) {

                // Pega os dados filtrados
                $data = $form->getData();

                // Executa a autenticação
                $adapter = $this->authService->getAdapter();
                $adapter->setEmail($data['email']);
                $adapter->setPassword($data['password']);
                $result = $this->authService->authenticate();

                // Verifica o resultado
                if ($result->getCode() == Result::SUCCESS) {
                    // Pega a URL a ser redirecionado
                    $redirectUrl = $this->params()->fromPost('redirect_url', '');

                    if (!empty($redirectUrl)) {
                        // Verifica se o URL é válida, evitando de ser
                        // redirecionado para outro domínio
                        $uri = new Uri($redirectUrl);
                        if (!$uri->isValid() || $uri->getHost() != null)
                            throw new \Exception('Incorrect redirect URL: '
                                . $redirectUrl);
                    }

                    if ($data['remember_me']) {
                        // A sessão expira em 1 mês (30 dias)
                        $this->authService->getStorage()
                            ->rememberMe(60 * 60 * 24 * 30);
                    }

                    $this->redirect()->toUrl($url);
                }

                // Se o usuário ainda não tiver verificado o e-mail
                elseif ($result->getCode() == Result::FAILURE) {
                    $this->flashMessenger()->addErrorMessage(
                        'Este usuário ainda não foi validado. ' .
                            'Verifique seu e-mail de validação e clique no ' .
                            'link para ativá-lo ou solicite um novo e-mail.'
                    );

                    // Redireciona para a tela de solicitação de
                    // envio de um novo e-mail de validação
                    return $this->redirect()
                        ->toRoute('sendVerificationEmail');
                } else {
                    $this->flashMessenger()->addErrorMessage(current($result->getMessages()));
                }
            } else if (isset($form->getMessages('csrf')['isEmpty'])) {
                return $this->redirect()->toRoute('not-authorized');
            } else {
                $this->flashMessenger()->addErrorMessage('Usuário ou senha inválidos');
            }
        }

        return new ViewModel([
            'form'         => $form,
            'redirectUrl'  => $url
        ]);
    }

    public function logoutAction()
    {
        if ($this->authService->hasIdentity()) {
            $this->authService->clearIdentity();
        }

        return $this->redirect()->toRoute('login');
    }

    public function notAuthorizedAction()
    {
        $this->getResponse()->setStatusCode(403);

        return new ViewModel();
    }
}
