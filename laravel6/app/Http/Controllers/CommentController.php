<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Post;
use App\Comment;

class CommentController extends Controller
{
    /**
     * Lista os comentários cadastrados
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Verifica se tem permissão, ou exibe uma tela de erro
        $this->authorize('manage', Comment::class);

        // Consulta os comentários com paginação e
        // ordenados do mais recente para o mais antigo
        $comments = Comment::orderBy('id', 'desc')->paginate(10);

        return view('comment.index', compact('comments'));
    }

    /**
     * Aprova um comentário para que ele seja exibido no blog
     * 
     * @param  int  $id
     * @return resource
     */
    public function approve(Request $request, $id)
    {
        $comment = comment::find($id); // Consulta um comentário pelo id

        // Verifica se o comentário existe,
        // ou exibe uma tela de erro
        if (!$comment) {
            abort(404);
        }

        // Verifica se tem permissão, ou exibe uma tela de erro
        $this->authorize('approve', $comment);

        try {
            $comment->approve();

            $msg = ['success' => __('Comment successfully approved')];
        } catch (\Exception $e) {
            $msg = ['error' => __('Error trying to approve a comment')];
        }

        // Volta à tela anterior com mensagem de erro
        return redirect()->back()->with($msg);
    }

    /**
     * Exclui um comentário
     * 
     * @param  int  $id
     * @return resource
     */
    public function destroy($id)
    {
        $comment = comment::find($id); // Consulta um comentário pelo id

        // Verifica se o comentário existe,
        // ou exibe uma tela de erro
        if (!$comment) {
            abort(404);
        }

        // Verifica se tem permissão, ou exibe uma tela de erro
        $this->authorize('delete', $comment);

        try {
            $postId = $comment->post_id;
            $comment->delete();

            $msg = ['success' => __('Comment successfully deleted')];
        } catch (\Exception $e) {
            $msg = ['error' => __('Error trying to delete a comment')];
        }

        // Volta à tela anterior com mensagem de erro ou sucesso
        return redirect()->back()->with($msg);
    }

    /**
     * Cadastra um novo comentário
     * 
     * @return resource
     */
    public function store(Request $request)
    {
        // Verifica se tem permissão, ou exibe uma tela de erro
        $this->authorize('create', Comment::class);

        // Verifica se os dados enviados são válidos, ou volta
        // para a tela anterior com as mensagens de erro
        // correspondentes a cada um dos campos validados
        $request->validate([
            'text'  => 'required|max:300'
        ]);

        // Consulta um post pelo id, aqui no caso é o post
        // relacionado ao comentário
        $post = Post::find($request->post_id);

        // Verifica se o post existe, ou exibe uma tela de erro
        if (!$post) {
            abort(404);
        }

        try {
            // Cria um novo comentário com os dados enviados
            $comment = new Comment();
            $comment->post_id = $request->post_id;
            $comment->user_id = Auth::id(); //Id do usuário logado
            $comment->text = $request->text;
            $comment->save();

            // Se for um administrador já aprova automaticamente
            if (Auth::user()->isAdmin()) {
                $comment->approve();
            }

            $msg = ['success' => __('Comment successfully added')];
        } catch (\Exception $e) {
            $msg = ['error' => __('Error trying to add a comment')];
        }

        // Volta à tela anterior com mensagem de erro ou sucesso
        return redirect()->back()->withInput()->with($msg);
    }
}
