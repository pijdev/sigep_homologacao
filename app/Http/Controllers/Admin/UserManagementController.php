<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permissao;
use App\Models\Setor;
use App\Models\UnidadePrisional;
use App\Models\User;
use App\Services\AclService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function __construct(private readonly AclService $acl)
    {
    }

    public function index(Request $request): View
    {
        $actor = $request->user();
        abort_unless($this->acl->canManageUsers($actor), 403);

        $users = $this->acl->visibleUsersQuery($actor)->paginate(15);

        return view('admin.users.index', [
            'users' => $users,
            'canCreate' => true,
        ]);
    }

    public function create(Request $request): View
    {
        $actor = $request->user();
        abort_unless($this->acl->canManageUsers($actor), 403);

        return view('admin.users.form', $this->buildFormData($actor, new User(), 'store'));
    }

    public function store(Request $request): RedirectResponse
    {
        $actor = $request->user();
        abort_unless($this->acl->canManageUsers($actor), 403);

        $validated = $this->validateUser($request, null, $actor);

        DB::transaction(function () use ($validated, $request, $actor) {
            $user = new User();
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->login = $validated['login'];
            $user->setor_id = $validated['setor_id'];
            $user->is_active = $request->boolean('is_active');
            $user->is_system_admin = $this->acl->canAssignSystemAdmin($actor)
                ? $request->boolean('is_system_admin')
                : false;
            $user->password = Hash::make($validated['password']);
            $user->save();

            $this->syncRelations($user, $validated, $actor);
        });

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Usuário criado com sucesso.');
    }

    public function edit(Request $request, User $user): View
    {
        $actor = $request->user();
        abort_unless($this->acl->canManageUsers($actor), 403);
        abort_unless($this->acl->canManageTargetUser($actor, $user), 403);

        $user->load(['unidades', 'usuarioPermissoes']);

        return view('admin.users.form', $this->buildFormData($actor, $user, 'update'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $actor = $request->user();
        abort_unless($this->acl->canManageUsers($actor), 403);
        abort_unless($this->acl->canManageTargetUser($actor, $user), 403);

        $validated = $this->validateUser($request, $user, $actor);

        DB::transaction(function () use ($validated, $request, $user, $actor) {
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->login = $validated['login'];
            $user->setor_id = $validated['setor_id'];
            $user->is_active = $request->boolean('is_active');

            if ($this->acl->canAssignSystemAdmin($actor)) {
                $user->is_system_admin = $request->boolean('is_system_admin');
            }

            if (! empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();

            $this->syncRelations($user, $validated, $actor);
        });

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Usuário atualizado com sucesso.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $actor = $request->user();
        abort_unless($this->acl->canManageUsers($actor), 403);
        abort_unless($this->acl->canManageTargetUser($actor, $user), 403);

        if ((int) $actor->id === (int) $user->id) {
            return redirect()
                ->route('admin.users.index')
                ->with('status', 'Você não pode excluir o próprio usuário logado.');
        }

        DB::transaction(function () use ($user) {
            $user->usuarioPermissoes()->delete();
            $user->unidades()->detach();
            $user->delete();
        });

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Usuário excluído com sucesso.');
    }

    private function buildFormData(User $actor, User $user, string $mode): array
    {
        $managedSectorIds = $this->acl->managedSectorIds($actor);
        $isSystemAdmin = $this->acl->isSystemAdmin($actor);

        $setores = Setor::query()
            ->when(! $isSystemAdmin, fn ($query) => $query->whereIn('id', $managedSectorIds))
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();

        $unidades = UnidadePrisional::query()->orderBy('nome')->get();

        $permissions = $this->acl->availablePermissionsFor($actor)
            ->groupBy(fn (Permissao $permission) => $permission->setor?->nome ?? 'Administrativo');

        $currentPermissions = $user->exists
            ? $user->usuarioPermissoes->keyBy('permissao_id')
            : collect();

        return [
            'mode' => $mode,
            'userModel' => $user,
            'setores' => $setores,
            'unidades' => $unidades,
            'permissions' => $permissions,
            'currentPermissions' => $currentPermissions,
            'canAssignSystemAdmin' => $this->acl->canAssignSystemAdmin($actor),
        ];
    }

    private function validateUser(Request $request, ?User $target, User $actor): array
    {
        $allowedSectorIds = $this->acl->isSystemAdmin($actor)
            ? Setor::query()->pluck('id')->all()
            : $this->acl->managedSectorIds($actor)->all();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($target?->id),
            ],
            'login' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'login')->ignore($target?->id),
            ],
            'setor_id' => ['required', Rule::in($allowedSectorIds)],
            'is_active' => ['nullable', 'boolean'],
            'is_system_admin' => ['nullable', 'boolean'],
            'password' => [$target ? 'nullable' : 'required', 'string', 'min:6', 'confirmed'],
            'unidades' => ['required', 'array', 'min:1'],
            'unidades.*' => ['integer', Rule::exists('unidades_prisionais', 'id')],
            'permissions' => ['nullable', 'array'],
            'permissions.*.nivel' => ['nullable', Rule::in(['none', 'read', 'write', 'owner'])],
            'permissions.*.unidade_id' => ['nullable', Rule::exists('unidades_prisionais', 'id')],
        ];

        $validated = $request->validate($rules);

        if (! $this->acl->canAssignSystemAdmin($actor)) {
            unset($validated['is_system_admin']);
        }

        return $validated;
    }

    private function syncRelations(User $user, array $validated, User $actor): void
    {
        $user->unidades()->sync($validated['unidades']);

        $assignments = collect($validated['permissions'] ?? [])
            ->map(function (array $data, string $permissionId) use ($actor) {
                $nivel = $data['nivel'] ?? 'none';
                if ($nivel === 'none') {
                    return null;
                }

                $permission = Permissao::query()->find((int) $permissionId);
                if (! $permission) {
                    return null;
                }

                if (! $this->acl->canAssignPermission($actor, $permission, $nivel)) {
                    return null;
                }

                return [
                    'permissao_id' => $permission->id,
                    'unidade_id' => ! empty($data['unidade_id']) ? (int) $data['unidade_id'] : null,
                    'nivel' => $nivel,
                    'permitido' => true,
                    'observacao' => null,
                ];
            })
            ->filter()
            ->values();

        $user->usuarioPermissoes()->delete();
        $user->usuarioPermissoes()->createMany($assignments->all());
    }
}
