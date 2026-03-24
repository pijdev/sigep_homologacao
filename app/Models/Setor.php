<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setor extends Model
{
    protected $table = 'autenticacao_setores';

    protected $fillable = [
        'slug',
        'nome',
        'descricao',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function usuarios()
    {
        return $this->hasMany(User::class, 'setor_id');
    }

    public function permissoes()
    {
        return $this->hasMany(Permissao::class, 'setor_id');
    }
}
