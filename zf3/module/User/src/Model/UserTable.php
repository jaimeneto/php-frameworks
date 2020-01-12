<?php

namespace User\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Db\Sql\Select;

// Classes necessárias para paginação
use Zend\Db\ResultSet\ResultSet;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Crypt\Password\Bcrypt;

class UserTable
{
    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    private function initSelect(&$select, array $where = null, $order = null) 
    {
        if ($order) {
            $select->order($order);
        }

        if ($where) {
            $select->where($where);
        }
    }

    public function fetchAll(array $where = null, $order = null)
    {
        return $this->tableGateway->select(
            function (Select $select) use ($where, $order) {
                $this->initSelect($select, $where, $order);
            }
        );
    }

    public function paginate($page = 1, $limit = 10, 
        $order = null, array $where = null) 
    {
        // Cria um novo objeto Select para a tabela
        $select = new Select($this->tableGateway->getTable());
        $this->initSelect($select, $where, $order);

        // Cria um novo ResultSet baseado no model User
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new User());

        // Cria um objeto adapter de paginação
        $paginatorAdapter = new DbSelect(
            $select,
            $this->tableGateway->getAdapter(),
            $resultSetPrototype
        );

        // Cria o objeto de paginação
        $paginator = new Paginator($paginatorAdapter);

        // Define o número de itens por página
        $paginator->setItemCountPerPage($limit);

        // Define o número da página atual
        $paginator->setCurrentPageNumber($page);

        return $paginator;
    }

    /**
     * Consulta um usuário específico a partir do id
     */
    public function find($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();
        if (!$row) {
            throw new RuntimeException(sprintf(
                'O usuário de id %d não existe',
                $id
            ));
        }

        return $row;
    }

    /**
     * Consulta um usuário específico a partir do email
     */
    public function findByEmail($email)
    {
        $rowset = $this->tableGateway->select(['email' => $email]);
        $row = $rowset->current();
        if (!$row) {
            throw new RuntimeException(sprintf(
                'O usuário de email %d não existe',
                $email
            ));
        }

        return $row;
    }

    /**
     * Insere ou atualiza um usuário na tabela do banco de dados
     */
    public function save(User $user)
    {
        $data = [
            'name'           => $user->name,
            'email'          => $user->email,
            'type'           => $user->type,
            'password'       => $user->password,
            'remember_token' => $user->remember_token,
            'updated_at'     => date('Y-m-d H:i:s')
        ];

        $id = (int) $user->id;

        // Se não tiver id, executa um insert
        if ($id === 0) {
            $data['created_at'] = $data['updated_at'];
            $data['type'] = 'user';
            return $this->tableGateway->insert($data);
        }

        try {
            $this->find($id);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                'Erro ao tentar atualizar. ' .
                    'O usuário de id %d não existe',
                $id
            ));
        }

        // executa um update
        return $this->tableGateway->update($data, ['id' => $id]);
    }

    /**
     * Registra a data e hora de último acesso do usuário
     */
    public function registerAccess($id)
    {
        try {
            $user = $this->find($id);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                'Erro ao tentar aprovar. ' .
                    'O usuário de id %d não existe',
                $id
            ));
        }

        $data = ['accessed_at' => date('Y-m-d H:i:s')];

        // executa um update
        return $this->tableGateway->update($data, ['id' => $id]);
    }

    /**
     * Define a data de exclusão do usuário, ou, se já tiver sido definida,
     * exclui o usuário da tabela do banco de dados
     */
    public function delete($id)
    {
        try {
            $user = $this->find($id);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                'Erro ao tentar excluir. ' .
                    'O usuário de id %d não existe',
                $id
            ));
        }

        // Exclui definitivamente o registro
        if ($user->deleted_at) {
            return $this->tableGateway->delete(['id' => (int)$id]);
        }

        // executa um update, definindo a data da exclusão
        $data = ['deleted_at' => date('Y-m-d H:i:s')];
        return $this->tableGateway->update($data, ['id' => $id]);
    }

    /**
     * Remove a data de exclusão do usuário, para restaurá-lo
     */
    public function restore($id)
    {
        try {
            $user = $this->find($id);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                'Erro ao tentar restaurar. ' .
                    'O usuário de id %d não existe',
                $id
            ));
        }

        // executa um update, removendo a data da exclusão
        $data = ['deleted_at' => null];
        return $this->tableGateway->update($data, ['id' => $id]);
    }

    /**
     * Verifica se o email é válido e, se for, define a data da verificação
     */
    public function verifyEmail($email, $code)
    {
        $user = $this->findByEmail($email);    

        if ($user->email_verified_at) {
            throw new \Exception('Este e-mail já foi validado');
        }

        $bcrypt = new Bcrypt();
        $userCode = $user->email . $user->password;

        if (!$bcrypt->verify($userCode, $code)) {
            throw new \Exception('Não foi possível validar o e-mail ' 
                . 'do usuário: Código inválido.');
        }

        // executa um update, atualizando a data de verificação do email
        $data = ['email_verified_at' => date('Y-m-d H:i:s')];
        return $this->tableGateway->update($data, ['id' => $user->id]);
    }

    public function resetPassword($email, $code) 
    {
        try {
            $user = $this->findByEmail($email);
        } catch (\Exception $e) {
            throw new \Exception('Usuário inválido');
        }

        $bcrypt = new Bcrypt();
        $userCode = $user->email . $user->password;

        $newPassword = substr($code, 60);
        $code1 = substr($code, 0, 60);

        if (!$bcrypt->verify($userCode, $code1)) {
            throw new \Exception('Não foi possível redefinir a senha: ' 
                . 'Código inválido.');
        }

        // executa um update, alterando a senha
        $user->password = $newPassword;

        if (!$user->email_verified_at) {
            $user->email_verified_at = date('Y-m-d H:i:s');
        }

        return $this->save($user);
    }
}