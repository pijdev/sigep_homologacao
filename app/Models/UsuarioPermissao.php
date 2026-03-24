<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioPermissao extends Model
{
    protected $table = 'autenticacao_usuario_permissao';

    protected $fillable = [
        'user_id',
        'permissao_id',
        'unidade_id',
        'nivel',
        'permitido',
        'observacao',
    ];

    protected $casts = [
        'permitido' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function permissao()
    {
        return $this->belongsTo(Permissao::class, 'permissao_id');
    }

    public function unidade()
    {
        return $this->belongsTo(UnidadePrisional::class, 'unidade_id');
    }
}
