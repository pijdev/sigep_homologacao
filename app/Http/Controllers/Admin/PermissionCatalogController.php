<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permissao;
use App\Models\Setor;
use App\Services\AclService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PermissionCatalogController extends Controller
{
    public function __construct(private readonly AclService $acl)
    {
    }

    public function index(Request $request): View
    {
        abort_unless($this->acl->canManageResources($request->user()), 403);

        return view('admin.permissions.index', [
            'permissoes' => Permissao::query()
                ->with(['setor', 'parent'])
                ->orderBy('tipo')
                ->orderBy('ordem')
                ->orderBy('nome')
                ->paginate(30),
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless($this->acl->canManageResources($request->user()), 403);

        return view('admin.permissions.form', [
            'permissao' => new Permissao(),
            'editing' => false,
            'setores' => Setor::query()->where('ativo', true)->orderBy('nome')->get(),
            'parents' => Permissao::query()->orderBy('codigo')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($this->acl->canManageResources($request->user()), 403);

        $validated = $this->validatePermission($request, null);

        Permissao::query()->create([
            ...$validated,
            'ativo' => $request->boolean('ativo'),
        ]);

        return redirect()->route('admin.permissions.index')->with('status', 'Recurso ACL criado com sucesso.');
    }

    public function edit(Request $request, Permissao $permission): View
    {
        abort_unless($this->acl->canManageResources($request->user()), 403);

        return view('admin.permissions.form', [
            'permissao' => $permission,
            'editing' => true,
            'setores' => Setor::query()->where('ativo', true)->orderBy('nome')->get(),
            'parents' => Permissao::query()
                ->whereKeyNot($permission->id)
                ->orderBy('codigo')
                ->get(),
        ]);
    }

    public function update(Request $request, Permissao $permission): RedirectResponse
    {
        abort_unless($this->acl->canManageResources($request->user()), 403);

        $validated = $this->validatePermission($request, $permission);

        $permission->update([
            ...$validated,
            'ativo' => $request->boolean('ativo'),
        ]);

        return redirect()->route('admin.permissions.index')->with('status', 'Recurso ACL atualizado com sucesso.');
    }

    public function destroy(Request $request, Permissao $permission): RedirectResponse
    {
        abort_unless($this->acl->canManageResources($request->user()), 403);

        if ($permission->children()->exists() || $permission->usuarios()->exists()) {
            return redirect()
                ->route('admin.permissions.index')
                ->with('status', 'Não foi possível excluir o recurso porque ele possui dependências ou usuários vinculados.');
        }

        $permission->delete();

        return redirect()->route('admin.permissions.index')->with('status', 'Recurso ACL excluído com sucesso.');
    }

    private function validatePermission(Request $request, ?Permissao $permission): array
    {
        return $request->validate([
            'parent_id' => ['nullable', Rule::exists('autenticacao_permissoes', 'id')],
            'setor_id' => ['nullable', Rule::exists('autenticacao_setores', 'id')],
            'codigo' => ['required', 'string', 'max:160', Rule::unique('autenticacao_permissoes', 'codigo')->ignore($permission?->id)],
            'nome' => ['required', 'string', 'max:160'],
            'tipo' => ['required', Rule::in(['menu', 'setor', 'subsetor', 'modulo', 'pagina', 'acao'])],
            'slug' => ['required', 'string', 'max:80', 'alpha_dash'],
            'descricao' => ['nullable', 'string', 'max:255'],
            'ordem' => ['nullable', 'integer', 'min:0'],
            'ativo' => ['nullable', 'boolean'],
        ]);
    }
}
