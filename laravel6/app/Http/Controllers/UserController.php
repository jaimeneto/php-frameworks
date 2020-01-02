<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{
    /**
     * Lista os usuários cadastrados
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Verifica se tem permissão, ou exibe uma tela de erro
        $this->authorize('manage', User::class);

        // Consulta usuários, inclusive os que estão na lixeira, em ordem
        // alfabética com contagem de comentários e posts, e paginação
        $users = User::withTrashed()
            ->withCount('posts')
            ->withCount('comments')
            ->orderBy('name')
            ->paginate(10);

        return view('user.index', compact('users'));
    }

    /**
     * Transforma um usuário comum em admin
     * 
     * @param  int  $id
     * @return resource
     */
    public function turnIntoAdmin($id)
    {
        $user = User::find($id);

        // Verifica se o usuário existe, ou exibe uma tela de erro
        if (!$user) {
            abort(404);
        }

        // Verifica se tem permissão, ou exibe uma tela de erro
        $this->authorize('changeRole', $user);

        try {
            $user->role = 'admin';
            $user->update();

            $msg = ['success' => 
                __('User role successfully changed to administrator')];
        } catch (\Exception $e) {
            $msg = ['error' => 
                __('Error trying to change user role to administrator')];
        }

        // Volta à tela anterior com mensagem de erro ou de sucesso
        return redirect()->back()->with($msg);
    }

    /**
     * Manda um usuário para a lixeira(define a data e hora que
     * foi excluído no campo deleted_at no banco de dados), ou,
     * se o parâmetro $force for true, exclui definitivamente
     * 
     * @param  int      $id
     * @param  boolean  $force
     * @return resource
     */
    public function destroy($id, $force = false)
    {
        // Consulta o usuário mesmo que esteja na lixeira
        $user = User::withTrashed()->find($id);

        // Verifica se o usuário existe, ou exibe uma tela de erro
        if (!$user) {
            abort(404);
        }

        // Verifica se tem permissão, ou exibe uma tela de erro
        $this->authorize($force ? 'forceDelete' : 'delete', $user);

        try {
            // Se force for true, exclui, senão manda para a lixeira
            $force ? $user->forceDelete() : $user->delete();
            $msg = ['success' => __('User successfully deleted')];
        } catch (\Exception $e) {
            $msg = ['error' => __('Error trying to delete a user')];
        }

        // Volta à tela anterior com mensagem de erro ou de sucesso
        return redirect()->back()->with($msg);
    }

    /**
     * Recupera um usuário da lixeira (anula o campo deleted_at)
     * 
     * @param  int  $id
     * @return resource
     */
    public function restore($id)
    {
        // Consulta o usuário mesmo que esteja na lixeira
        $user = User::withTrashed()->find($id);

        // Verifica se o usuário existe, ou exibe uma tela de erro
        if (!$user) {
            abort(404);
        }

        // Verifica se tem permissão, ou exibe uma tela de erro
        $this->authorize('restore', $user);

        try {
            $user->restore();
            $msg = ['success' => __('User successfully restored')];
        } catch (\Exception $e) {
            $msg = ['error' => __('Error trying to restore a user')];
        }

        // Volta à tela anterior com mensagem de erro ou de sucesso
        return redirect()->back()->with($msg);
    }
}
