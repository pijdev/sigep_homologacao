<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioUnidade extends Model
{
    protected $table = 'autenticacao_usuario_unidade';

    protected $fillable = [
        'user_id',
        'unidade_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function unidade()
    {
        return $this->belongsTo(UnidadePrisional::class, 'unidade_id');
    }
}
