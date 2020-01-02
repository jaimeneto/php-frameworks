<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Comment extends Model
{
    const UPDATED_AT = null;

    /**
     * Relaciona o comentário a seu autor
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Relaciona o comentário ao post relacionado
     */
    public function post()
    {
        return $this->belongsTo('App\Post');
    }

    /**
     * Verifica se o comentário já foi aprovado
     */
    public function isApproved()
    {
        return $this->approved_at !== null;
    }

    /**
     * Aprova o comentário ao definir sua data de aprovação
     */
    public function approve()
    {
        $this->approved_at = now();
        $this->update();
    }

    /**
     * Faz com que ao acessar o atributo approved_at retorne um objeto
     * de data, se estiver preenchido, ou nulo se não estiver
     */
    public function getApprovedAtAttribute($date)
    {
        return $date ? Carbon::parse($date) : null;
    }
}
