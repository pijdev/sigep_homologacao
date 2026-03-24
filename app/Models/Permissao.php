<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permissao extends Model
{
    protected $table = 'autenticacao_permissoes';

    protected $fillable = [
        'parent_id',
        'setor_id',
        'codigo',
        'nome',
        'tipo',
        'slug',
        'descricao',
        'ordem',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function setor()
    {
        return $this->belongsTo(Setor::class, 'setor_id');
    }

    public function usuarios()
    {
        return $this->belongsToMany(
            User::class,
            'autenticacao_usuario_permissao',
            'permissao_id',
            'user_id'
        )->withPivot(['id', 'unidade_id', 'nivel', 'permitido', 'observacao'])
            ->withTimestamps();
    }
}
