<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UnidadePrisional;
use App\Services\AclService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UnitManagementController extends Controller
{
    public function __construct(private readonly AclService $acl)
    {
    }

    public function index(Request $request): View
    {
        abort_unless($this->acl->canManageUnits($request->user()), 403);

        return view('admin.units.index', [
            'unidades' => UnidadePrisional::query()
                ->withCount('usuarios')
                ->orderBy('nome')
                ->paginate(25),
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless($this->acl->canManageUnits($request->user()), 403);

        return view('admin.units.form', [
            'unidade' => new UnidadePrisional(),
            'editing' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($this->acl->canManageUnits($request->user()), 403);

        $validated = $request->validate([
            'id' => ['required', 'integer', Rule::unique('unidades_prisionais', 'id')],
            'nome' => ['required', 'string', 'max:255'],
        ]);

        UnidadePrisional::query()->create($validated);

        return redirect()->route('admin.units.index')->with('status', 'Unidade criada com sucesso.');
    }

    public function edit(Request $request, UnidadePrisional $unidade): View
    {
        abort_unless($this->acl->canManageUnits($request->user()), 403);

        return view('admin.units.form', [
            'unidade' => $unidade,
            'editing' => true,
        ]);
    }

    public function update(Request $request, UnidadePrisional $unidade): RedirectResponse
    {
        abort_unless($this->acl->canManageUnits($request->user()), 403);

        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
        ]);

        $unidade->update($validated);

        return redirect()->route('admin.units.index')->with('status', 'Unidade atualizada com sucesso.');
    }

    public function destroy(Request $request, UnidadePrisional $unidade): RedirectResponse
    {
        abort_unless($this->acl->canManageUnits($request->user()), 403);

        if ($unidade->usuarios()->exists()) {
            return redirect()
                ->route('admin.units.index')
                ->with('status', 'Não foi possível excluir a unidade porque ela ainda possui usuários vinculados.');
        }

        if ($unidade->usuarioPermissoes()->exists()) {
            return redirect()
                ->route('admin.units.index')
                ->with('status', 'Não foi possível excluir a unidade porque ela ainda possui permissões ACL vinculadas.');
        }

        $unidade->delete();

        return redirect()->route('admin.units.index')->with('status', 'Unidade excluída com sucesso.');
    }
}
