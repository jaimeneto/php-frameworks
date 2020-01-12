<?php

namespace User\Service;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;
use Zend\Crypt\Password\Bcrypt;
use User\Model\UserTable;

class AuthAdapter implements AdapterInterface
{
    private $email;
    private $password;
    private $table;

    public function __construct(UserTable $table)
    {
        $this->table = $table;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setPassword($password)
    {
        $this->password = (string)$password;
    }

    /**
     * Faz uma tentativa de autenticação
     *
     * @return \Zend\Authentication\Result
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface
     *     Se não for possível efetuar a autenticação
     */
    public function authenticate()
    {
        // Check the database if there is a user with such email.
        try {
            $user = $this->table->findByEmail($this->email);
        } catch(\Exception $e) {
            // Se o usuário não existe retorna um erro de "usuário não encontrado"
            return new Result(
                Result::FAILURE_IDENTITY_NOT_FOUND,
                null,
                ['Usuário ou senha inválidos']
            );
        }

        // Se o usuário existe, verifica se o e-mail já foi validado
        if (empty($user->getEmailVerifiedAt())) {
            return new Result(
                Result::FAILURE,
                null,
                ['O e-mail do usuário ainda não foi verificado']
            );
        }

        // Criptografa a senha para ser comparada com que está no banco de dados
        $bcrypt = new Bcrypt();
        $passwordHash = $user->password;

        if ($bcrypt->verify($this->password, $passwordHash)) {
            // Registra data e hora do último acesso
            $this->table->registerAccess($user->id);

            // Se o password estiver correto retorna os dados do usuário
            // para serem salvos na sessão para uso posterior
            return new Result(
                Result::SUCCESS,
                (Object) [
                    'id'    => $user->id,
                    'email' => $this->email, 
                    'name'  => $user->name,
                    'type'  => $user->type
                ],
                ['Autenticação feita com sucesso']
            );
        }

        // Se a senha não estiver correta retorna um erro de credenciais inválidas
        return new Result(
            Result::FAILURE_CREDENTIAL_INVALID,
            null,
            ['Usuário ou senha inválidos']
        );
    }
}