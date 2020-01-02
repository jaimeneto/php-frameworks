<?php

namespace App\Policies;

use App\Post;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    /**
     * Apenas administradores podem administrar os posts
     */
    public function manage(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Todos os usuários podem visualizar os posts
     */
    public function view(?User $user, Post $post)
    {
        return true;
    }

    /**
     * Apenas administradores podem cadastrar novos posts
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Apenas administradores podem alterar os usuários
     */
    public function update(User $user, Post $post)
    {
        return $user->isAdmin();
    }

    /**
     * Apenas administradores podem excluir os usuários
     */
    public function delete(User $user, Post $post)
    {
        return $user->isAdmin();
    }
    
}
