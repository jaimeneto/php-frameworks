<?php

namespace Blog\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Db\Sql\Select;

// Classes necessárias para paginação
use Zend\Db\ResultSet\ResultSet;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class CommentTable
{
    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    private function initSelect(&$select, array $where = null, $order = null) {
        if ($order) {
            $select->order($order);
        }

        if ($where) {
            $select->where($where);
        }

        // Pega o nome do autor de cada comentário na tabela de usuários
        $select->join(
            ['u' => 'users'],
            'u.id = comments.user_id',
            ['user_name' => 'name'],
            $select::JOIN_INNER
        );

        // Pega o título do post de cada comentário
        $select->join(
            ['p' => 'posts'],
            'p.id = comments.post_id',
            ['post_title' => 'title'],
            $select::JOIN_INNER
        );
    }

    public function fetchAll(array $where = null, $order = null)
    {
        return $this->tableGateway->select(
            function (Select $select) use ($where, $order) {
                $this->initSelect($select, $where, $order);
            }
        );
    }

    public function paginate(
        $page = 1,
        $limit = 10,
        $order = null,
        array $where = null
    ) {
        // Cria um novo objeto Select para a tabela
        $select = new Select($this->tableGateway->getTable());
        $this->initSelect($select, $where, $order);

        // Cria um novo ResultSet baseado no model Comment
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new Comment());

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
     * Consulta um comentário específico a partir do id
     */
    public function find($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();
        if (!$row) {
            throw new RuntimeException(sprintf(
                'O comentário de id %d não existe',
                $id
            ));
        }

        return $row;
    }

    /**
     * Insere ou atualiza um comentário na tabela do banco de dados
     */
    public function save(Comment $comment)
    {
        $data = [
            'text' => $comment->text
        ];

        $id = (int)$comment->id;

        // Se não tiver id, executa um insert
        if ($id === 0) {
            $data['post_id'] = $comment->post_id;
            $data['user_id'] = $comment->user_id;
            $data['created_at'] = date('Y-m-d H:i:s');

            return $this->tableGateway->insert($data);
        }

        try {
            $this->find($id);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                'Erro ao tentar atualizar. ' .
                    'O comentário de id %d não existe',
                $id
            ));
        }

        // executa um update
        return $this->tableGateway->update($data, ['id' => $id]);
    }

    public function approve($id)
    {
        try {
            $comment = $this->find($id);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                'Erro ao tentar aprovar. ' .
                    'O comentário de id %d não existe',
                $id
            ));
        }

        $data = ['approved_at' => date('Y-m-d H:i:s')];

        // executa um update
        return $this->tableGateway->update($data, ['id' => $id]);
    }

    /**
     * Exclui um comentário da tabela do banco de dados
     */
    public function delete($id)
    {
        $this->tableGateway->delete(['id' => (int)$id]);
    }
}