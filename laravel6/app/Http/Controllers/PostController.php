<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    private $validators = [
        'title' => 'required|unique:posts',
        'text'  => 'required|min:100'
    ];

    /**
     * Lista os posts cadastrados
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Verifica se tem permissão, ou exibe uma tela de erro
        $this->authorize('create', Post::class);

        // Consulta posts com contagem de comentários e paginação
        // ordenado do mais recente para o mais antigo
        $posts = Post::orderBy('id', 'desc')
            ->withCount('comments')
            ->paginate(10);

        return view('post.index', compact('posts'));
    }


    /**
     * Exibe um formulário para cadastrar um novo post
     * 
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Verifica se tem permissão, ou exibe uma tela de erro
        $this->authorize('create', Post::class);

        // Cria um objeto Post vazio
        $post = new Post();

        return view('post.form', compact('post'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return resource
     */
    public function store(Request $request)
    {
        // Verifica se tem permissão, ou exibe uma tela de erro
        $this->authorize('create', Post::class);

        // Verifica se os dados enviados são válidos, ou volta para
        // a tela anterior com as mensagens de erro correspondentes a
        // cada um dos campos validados
        $request->validate($this->validators);

        try {
            // cria um novo post com os dados enviados
            $post = new Post();
            $post->title = $request->title;
            $post->text = $request->text;
            $post->user_id = Auth::id(); //Id do usuário logado
            $post->save();

            // Redireciona para a tela de editar post com uma
            // mensagem de sucesso
            return redirect()
                ->route('post.edit', [$post->id])
                ->with(['success' => __('Post successfully added')]);
        } catch (\Exception $e) { }

        // Volta à tela anterior com mensagem de erro
        return redirect()->back()->withInput()
            ->with(['error' => __('Error trying to add a post')]);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Consulta um post pelo id
        $post = Post::find($id);

        // Verifica se o post existe, ou exibe uma tela de erro
        if (!$post) {
            abort(404);
        }

        // Verifica se tem permissão, ou exibe uma tela de erro
        $this->authorize('view', $post);

        return view('post.show', compact('post'));
    }


    /**
     * Exibe um formulário para alterar os dados de um post
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Consulta um post pelo id
        $post = Post::find($id);

        // Verifica se o post existe, ou exibe uma tela de erro
        if (!$post) {
            abort(404);
        }

        // Verifica se tem permissão, ou exibe uma tela de erro
        $this->authorize('update', $post);

        return view('post.form', compact('post'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return resource
     */
    public function update(Request $request, $id)
    {
        // Consulta um post pelo id
        $post = Post::find($id);

        // Verifica se o post existe, ou exibe uma tela de erro
        if (!$post) {
            abort(404);
        }

        // Verifica se tem permissão, ou exibe uma tela de erro
        $this->authorize('update', $post);

        try {
            // Altera os dados do post
            $post->title = $request->title;
            $post->text = $request->text;
            $post->update();

            $msg = ['success' => __('Post successfully updated')];
        } catch (\Exception $e) {
            $msg = ['error' => __('Error trying to update a post')];
        }

        // Volta à tela anterior com mensagem de erro ou de sucesso
        return redirect()->back()->withInput()->with($msg);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return resource
     */
    public function destroy($id)
    {
        // Consulta um post pelo id
        $post = Post::find($id);

        // Verifica se o post existe, ou exibe uma tela de erro
        if (!$post) {
            abort(404);
        }

        // Verifica se tem permissão, ou exibe uma tela de erro
        $this->authorize('delete', $post);

        try {
            $post->delete();
            $msg = ['success' => __('Post successfully deleted')];
        } catch (\Exception $e) {
            $msg = ['error' => __('Error trying to delete a post')];
        }

        // Volta à tela anterior com mensagem de erro ou de sucesso
        return redirect()->back()->with($msg);
    }
}
