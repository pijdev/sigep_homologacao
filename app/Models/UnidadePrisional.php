<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnidadePrisional extends Model
{
    protected $table = 'unidades_prisionais';

    protected $fillable = [
        'id',
        'nome',
    ];

    public $incrementing = false;

    protected $keyType = 'int';

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'autenticacao_usuario_unidade', 'unidade_id', 'user_id');
    }

    public function usuarioPermissoes()
    {
        return $this->hasMany(UsuarioPermissao::class, 'unidade_id');
    }
}
