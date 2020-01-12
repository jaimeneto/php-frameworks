<?php

namespace User\Model;

use User\AssertionsManager;

class UserAssertions extends AssertionsManager
{
    /**
     * Regras de permissão para user.restore
     */
    public function assertUserRestore()
    {
        if (!isset($this->params['user'])) {
            return false;
        }

        $user = $this->params['user'];

        // Se o usuário foi excluído (está na lixeira)
        return (null !== $user->deleted_at);
    }

    /**
     * Regras de permissão para user.destroy
     */
    public function assertUserDestroy()
    {
        if (!isset($this->params['user'])) {
            return false;
        }

        $user = $this->params['user'];

        // Se o usuário foi excluído (está na lixeira)
        return ($user->type !== 'admin' 
            && null !== $user->deleted_at);
    }

    /**
     * Regras de permissão para user.delete
     */
    public function assertUserDelete()
    {
        if (!isset($this->params['user'])) {
            return false;
        }

        $user = $this->params['user'];

        // Se o usuário não é administrador e
        // se não foi excluído (não está na lixeira)
        return ($user->type !== 'admin'
            && null === $user->deleted_at);
    }

    /**
     * Regras de permissão para user.turnIntoAdmin
     */
    public function assertUserTurnIntoAdmin()
    {
        if (!isset($this->params['user'])) {
            return false;
        }

        $user = $this->params['user'];

        // Se o usuário não é administrador
        return ($user->type !== 'admin');
    }

}