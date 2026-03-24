<?php

namespace App\Services;

use App\Models\Permissao;
use App\Models\Setor;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AclService
{
    /**
     * Ordem hierárquica dos níveis de acesso.
     *
     * @var array<string, int>
     */
    private const LEVEL_RANK = [
        'read' => 1,
        'write' => 2,
        'owner' => 3,
    ];

    public function isSystemAdmin(?User $user): bool
    {
        return (bool) ($user?->is_system_admin);
    }

    public function managedSectorIds(?User $user): Collection
    {
        if (! $user) {
            return collect();
        }

        if ($this->isSystemAdmin($user)) {
            return Setor::query()->pluck('id');
        }

        return $user->permissoes()
            ->where('autenticacao_usuario_permissao.nivel', 'owner')
            ->where('autenticacao_permissoes.tipo', 'setor')
            ->pluck('autenticacao_permissoes.setor_id')
            ->filter()
            ->unique()
            ->values();
    }

    public function canManageUsers(?User $user): bool
    {
        return $this->isSystemAdmin($user) || $this->managedSectorIds($user)->isNotEmpty();
    }

    public function canManageCatalog(?User $user): bool
    {
        return $this->isSystemAdmin($user);
    }

    public function canManageSectors(?User $user): bool
    {
        return $this->canManageCatalog($user);
    }

    public function canManageUnits(?User $user): bool
    {
        return $this->canManageCatalog($user);
    }

    public function canManageResources(?User $user): bool
    {
        return $this->canManageCatalog($user);
    }

    public function visibleUsersQuery(User $user): Builder
    {
        $query = User::query()
            ->with(['setor', 'unidades', 'permissoes'])
            ->orderBy('name');

        if ($this->isSystemAdmin($user)) {
            return $query;
        }

        $managedSectorIds = $this->managedSectorIds($user);

        return $query
            ->where('is_system_admin', false)
            ->whereIn('setor_id', $managedSectorIds);
    }

    public function canManageTargetUser(User $actor, User $target): bool
    {
        if ($this->isSystemAdmin($actor)) {
            return true;
        }

        if ($this->isSystemAdmin($target) || ! $target->setor_id) {
            return false;
        }

        return $this->managedSectorIds($actor)->contains($target->setor_id);
    }

    public function availablePermissionsFor(User $user): Collection
    {
        $query = Permissao::query()
            ->where('ativo', true)
            ->whereIn('tipo', ['setor', 'subsetor', 'modulo', 'pagina', 'acao'])
            ->with('setor')
            ->orderBy('tipo')
            ->orderBy('nome');

        if ($this->isSystemAdmin($user)) {
            return $query->get();
        }

        $managedSectorIds = $this->managedSectorIds($user);

        return $query
            ->where(function (Builder $builder) use ($managedSectorIds) {
                $builder
                    ->whereNull('setor_id')
                    ->orWhereIn('setor_id', $managedSectorIds);
            })
            ->get();
    }

    public function canAssignSystemAdmin(User $actor): bool
    {
        return $this->isSystemAdmin($actor);
    }

    public function canAssignPermission(User $actor, Permissao $permissao, string $nivel): bool
    {
        if (! isset(self::LEVEL_RANK[$nivel])) {
            return false;
        }

        if ($this->isSystemAdmin($actor)) {
            return true;
        }

        if (! $permissao->setor_id) {
            return false;
        }

        if (! $this->managedSectorIds($actor)->contains($permissao->setor_id)) {
            return false;
        }

        return $nivel !== 'owner';
    }

    public function userHasPermission(
        ?User $user,
        string $permissionCode,
        string $requiredLevel = 'read',
        ?int $unidadeId = null
    ): bool {
        if (! $user) {
            return false;
        }

        if ($this->isSystemAdmin($user)) {
            return true;
        }

        $requiredRank = self::LEVEL_RANK[$requiredLevel] ?? 0;

        $permission = Permissao::query()
            ->where('codigo', $permissionCode)
            ->first();

        if (! $permission) {
            return false;
        }

        $assignments = $user->usuarioPermissoes()
            ->where('permissao_id', $permission->id)
            ->where('permitido', true)
            ->when($unidadeId, function (Builder $builder) use ($unidadeId) {
                $builder->where(function (Builder $subQuery) use ($unidadeId) {
                    $subQuery->whereNull('unidade_id')
                        ->orWhere('unidade_id', $unidadeId);
                });
            })
            ->get();

        foreach ($assignments as $assignment) {
            $rank = self::LEVEL_RANK[$assignment->nivel] ?? 0;
            if ($rank >= $requiredRank) {
                return true;
            }
        }

        return false;
    }
}
