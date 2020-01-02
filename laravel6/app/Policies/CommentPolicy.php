<?php

namespace App\Policies;

use App\Comment;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    /**
     * Apenas administradores podem administrar os comentários
     */
    public function manage(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Um comentário só pode ser visualizado por qualquer usuário
     * se for aprovado, exceto se o usuário logado for o próprio autor
     * ou um administrador
     */
    public function view(?User $user, Comment $comment)
    {
        return $comment->isApproved() || ($user && ($comment->user_id === $user->id ||
                $user->isAdmin()));
    }

    /**
     * Qualquer usuário logado pode criar um comentário
     */
    public function create(User $user)
    {
        return $user->role !== null;
    }

    /**
     * Apenas administradores podem aprovar um comentário
     * que ainda não esteja aprovado
     */
    public function approve(User $user, Comment $comment)
    {
        return !$comment->isApproved() && $user->isAdmin();
    }

    /**
     * Um comentário pode ser excluído pelo próprio autor
     * ou por um administrador
     */
    public function delete(User $user, Comment $comment)
    {
        return $comment->user_id === $user->id
            || $user->isAdmin();
    }
}
