<?php

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;


class CommentsController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->tag->prependTitle('Comentários | ');

        $paginator = new Paginator([
            'data' => Comments::find(['order' => 'id DESC']),
            'limit' => 10,
            'page' => $this->request->getQuery('page', 'int')
        ]);

        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Creates a new comment
     */
    public function createAction()
    {
        $postId = $this->request->getPost('post_id');

        $redirectTo = [
            'controller' => 'index',
            'action' => 'showPost',
            'params' => [$postId]
        ];

        if (!$this->request->isPost() || !$this->security->checkToken()) {
            return $this->dispatcher->forward([
                'controller' => 'error',
                'action' => 'show403'
            ]);
        }

        $comment = new Comments();
        $comment->setText($this->request->getPost("text"));
        $comment->setPostId($postId);
        $comment->setUserId($this->session->get('auth')->id);

        if (!$comment->save()) {
            foreach ($comment->getMessages() as $message) {
                $this->flash->error($message);
            }
            $this->flash->error('Ocorreu um erro ao tentar salvar o comentário');

            return $this->dispatcher->forward($redirectTo);
        }

        $this->flash->success('Comentário salvo com sucesso');

        $this->dispatcher->forward($redirectTo);
    }


    /**
     * Deletes a comment
     *
     * @param string $id
     */
    public function deleteAction($id, $postIdToRedirect = false)
    {
        $redirectTo = $postIdToRedirect
            ? [
                'controller' => "index",
                'action' => 'showPost',
                'params' => [$postIdToRedirect]
            ] : [
                'controller' => "comments",
                'action' => 'index'
            ];

        $comment = Comments::findFirstById($id);
        if (!$comment) {
            $this->flash->error("Comentário não encontrado");

            $this->dispatcher->forward($redirectTo);
            return;
        }

        if (!$comment->delete()) {
            foreach ($comment->getMessages() as $message) {
                $this->flash->error($message);
            }
            $this->dispatcher->forward($redirectTo);
            return;
        }

        $this->flash->success("Comentário excluído com sucesso");

        $this->dispatcher->forward($redirectTo);
    }

    public function approveAction($id, $postIdToRedirect = false)
    {
        $redirectTo = $postIdToRedirect
            ? [
                'controller' => "index",
                'action' => 'showPost',
                'params' => [$postIdToRedirect]
            ] : [
                'controller' => "comments",
                'action' => 'index'
            ];

        $comment = Comments::findFirstById($id);
        if (!$comment) {
            $this->flash->error("Comentário não encontrado");

            $this->dispatcher->forward($redirectTo);
            return;
        }

        $comment->setApprovedAt(date('Y-m-d H:i:s'));
        if (!$comment->save()) {
            foreach ($comment->getMessages() as $message) {
                $this->flash->error($message);
            }
            $this->dispatcher->forward($redirectTo);
            return;
        }

        $this->flash->success("Comentário aprovado com sucesso");

        $this->dispatcher->forward($redirectTo);
    }
}
