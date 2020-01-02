<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Apenas administradores podem administrar os usuários
     */
    public function manage(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Apenas admins podem alterar o tipo de um usuário,
     * mas apenas se o usuário já não for admin,
     * e se não estiver excluído
     */
    public function changeRole(User $user, User $model)
    {
        return !$model->trashed()
            && !$model->isAdmin()
            && $user->isAdmin();
    }


    /**
     * Apenas admins podem enviar um usuário para a lixeira,
     * mas apenas se o usuário não for admin também,
     * se já não estiver na lixeira
     * e se não for ele próprio
     */
    public function delete(User $user, User $model)
    {
        return !$model->trashed() &&
            $user->id !== $model->id &&
            $user->isAdmin() &&
            !$model->isAdmin();
    }


    /**
     * Apenas admins podem restaurar um usuário,
     * mas apenas se o usuário estiver na lixeira
     */
    public function restore(User $user, User $model)
    {
        return $model->trashed()
            && $user->isAdmin();
    }


    /**
     * Apenas admins podem excluir definitivamente um usuário,
     * mas apenas se o usuário já estiver na lixeira
     * e se não for ele próprio
     */
    public function forceDelete(User $user, User $model)
    {
        return $model->trashed()
            && $user->isAdmin()
            && $user->id !== $model->id;
    }
}
