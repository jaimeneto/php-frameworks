<?php

namespace Blog\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;

// Classes necessárias para paginação
use Zend\Db\ResultSet\ResultSet;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class PostTable
{
    /**
     * Permite fazer operações na tabela do banco de dados
     */
    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    private function initSelect(&$select, $order = null)
    {
        if ($order) {
            $select->order($order);
        }

        // Pega o nome do autor de cada post na tabela de usuários
        $select->join(
            ['u' => 'users'],
            'u.id = posts.user_id',
            ['user_name' => 'name'],
            $select::JOIN_INNER
        );

        // Cria um sub-select para contar os comentários de cada post
        $comments = new Select('comments');
        $comments->columns(['total' => new Expression('COUNT(*)')]);
        $comments->where(['post_id' => new Expression('posts.id')]);

        $select->columns([
            '*',
            'comments_count' => new Expression('?', [$comments])
        ]);
    }

    public function fetchAll($order = null)
    {
        return $this->tableGateway->select(
            function (Select $select) use ($order) {
                $this->initSelect($select, $order);
            }
        );
    }

    public function paginate($page = 1, $limit = 10, $order = null)
    {
        // Cria um novo objeto Select para a tabela
        $select = new Select($this->tableGateway->getTable());
        $this->initSelect($select, $order);

        // Cria um novo ResultSet baseado no model Post
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new Post());

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
     * Consulta um post específico a partir do id
     */
    public function find($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();
        if (!$row) {
            throw new RuntimeException(sprintf(
                'O post de id %d não existe',
                $id
            ));
        }

        return $row;
    }

    /**
     * Insere ou atualiza um post na tabela do banco de dados
     */
    public function save(Post $post)
    {
        $data = [
            'title'      => $post->title,
            'text'       => $post->text,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $id = (int)$post->id;

        // Se não tiver id, executa um insert
        if ($id === 0) {
            // Define o id do usuário que criou o post
            $data['user_id'] = $post->user_id;

            // Define a data de criação do post
            $data['created_at'] = $data['updated_at'];

            return $this->tableGateway->insert($data);
        }

        try {
            $this->find($id);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                'Erro ao tentar atualizar. O post de id %d não existe',
                $id
            ));
        }

        // executa um update
        return $this->tableGateway->update($data, ['id' => $id]);
    }

    /**
     * Exclui um post da tabela do banco de dados
     */
    public function delete($id)
    {
        $this->tableGateway->delete(['id' => (int)$id]);
    }
}
