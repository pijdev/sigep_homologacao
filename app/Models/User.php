<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Permissao;
use App\Models\Setor;
use App\Models\UnidadePrisional;
use App\Models\UsuarioPermissao;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'email',
    'password',
    'login',
    'setor_id',
    'is_system_admin',
    'is_active',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function unidades()
    {
        return $this->belongsToMany(UnidadePrisional::class, 'autenticacao_usuario_unidade', 'user_id', 'unidade_id');
    }

    public function setor()
    {
        return $this->belongsTo(Setor::class, 'setor_id');
    }

    public function usuarioPermissoes()
    {
        return $this->hasMany(UsuarioPermissao::class, 'user_id');
    }

    public function permissoes()
    {
        return $this->belongsToMany(
            Permissao::class,
            'autenticacao_usuario_permissao',
            'user_id',
            'permissao_id'
        )->withPivot(['id', 'unidade_id', 'nivel', 'permitido', 'observacao'])
            ->withTimestamps();
    }

    /**
     * Verificar se usuário tem acesso à unidade
     */
    public function temAcessoUnidade($unidadeId)
    {
        return $this->unidades()->where('unidade_id', $unidadeId)->exists();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_system_admin' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
