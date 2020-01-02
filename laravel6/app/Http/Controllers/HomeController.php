<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use Illuminate\Database\Eloquent\Builder;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware(['auth', 'verified']);
    }

    /**
     * Exibe a tela inicial do sistema
     */
    public function index()
    {
        // Consulta os posts cadastrados, com contagem de comentários 
        // paginação simples, e ordenados do mais recente para o mais
        // antigo
        $posts = Post::orderBy('id', 'desc')
            ->withCount(['comments' => function(Builder $query) {
                $query->whereNotNull('approved_at');
            }])
            ->simplePaginate(10);

        return view('home', compact('posts'));
    }
}
