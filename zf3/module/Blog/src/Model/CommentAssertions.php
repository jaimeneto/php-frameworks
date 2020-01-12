<?php

namespace Blog\Model;

use User\AssertionsManager;

class CommentAssertions extends AssertionsManager
{
    /**
     * Regras de permissão para blog.comment.approve
     */
    public function assertBlogCommentApprove()
    {
        if (!isset($this->params['comment'])) {
            return false;
        }

        $comment = $this->params['comment'];

        // Comentário ainda não foi aprovado
        return !$comment->isApproved();
    }

    /**
     * Regras de permissão para blog.comment.view
     */
    public function assertBlogCommentView()
    {
        if (!isset($this->params['comment'])) {
            return false;
        }

        $comment = $this->params['comment'];

        $identity = $this->authService->hasIdentity()
            ? $this->authService->getIdentity()
            : null;

        // Comentário ainda não aprovado ou
        // Usuário autenticado é o autor do comentário ou
        // Usuário autenticado é administrador
        return $comment->isApproved() || ($identity && (
                $identity->id === $comment->user_id   
                || $this->role->getName() === 'admin'
            ));
    }

    /**
     * Regras de permissão para blog.comment.delete
     */
    public function assertBlogCommentDelete()
    {
        if (!isset($this->params['comment'])) {
            return false;
        }

        $identity = $this->authService->hasIdentity()
            ? $this->authService->getIdentity()
            : null;

        $comment = $this->params['comment'];

        // Usuário autenticado é o autor do comentário ou
        // Usuário autenticado é administrador
        return ($identity && (
                $identity->id === $comment->user_id   
                || $identity->type === 'admin'
            ));
    }

}