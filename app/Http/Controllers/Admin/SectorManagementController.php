<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setor;
use App\Services\AclService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SectorManagementController extends Controller
{
    public function __construct(private readonly AclService $acl)
    {
    }

    public function index(Request $request): View
    {
        abort_unless($this->acl->canManageSectors($request->user()), 403);

        return view('admin.sectors.index', [
            'setores' => Setor::query()
                ->withCount(['usuarios', 'permissoes'])
                ->orderBy('nome')
                ->paginate(20),
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless($this->acl->canManageSectors($request->user()), 403);

        return view('admin.sectors.form', [
            'setor' => new Setor(),
            'editing' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($this->acl->canManageSectors($request->user()), 403);

        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:120'],
            'slug' => ['required', 'string', 'max:60', 'alpha_dash', Rule::unique('autenticacao_setores', 'slug')],
            'descricao' => ['nullable', 'string', 'max:255'],
            'ativo' => ['nullable', 'boolean'],
        ]);

        Setor::query()->create([
            ...$validated,
            'ativo' => $request->boolean('ativo'),
        ]);

        return redirect()->route('admin.sectors.index')->with('status', 'Setor criado com sucesso.');
    }

    public function edit(Request $request, Setor $setor): View
    {
        abort_unless($this->acl->canManageSectors($request->user()), 403);

        return view('admin.sectors.form', [
            'setor' => $setor,
            'editing' => true,
        ]);
    }

    public function update(Request $request, Setor $setor): RedirectResponse
    {
        abort_unless($this->acl->canManageSectors($request->user()), 403);

        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:120'],
            'slug' => ['required', 'string', 'max:60', 'alpha_dash', Rule::unique('autenticacao_setores', 'slug')->ignore($setor->id)],
            'descricao' => ['nullable', 'string', 'max:255'],
            'ativo' => ['nullable', 'boolean'],
        ]);

        $setor->update([
            ...$validated,
            'ativo' => $request->boolean('ativo'),
        ]);

        return redirect()->route('admin.sectors.index')->with('status', 'Setor atualizado com sucesso.');
    }

    public function destroy(Request $request, Setor $setor): RedirectResponse
    {
        abort_unless($this->acl->canManageSectors($request->user()), 403);

        if ($setor->usuarios()->exists() || $setor->permissoes()->exists()) {
            return redirect()
                ->route('admin.sectors.index')
                ->with('status', 'Não foi possível excluir o setor porque ele ainda possui usuários ou permissões vinculadas.');
        }

        $setor->delete();

        return redirect()->route('admin.sectors.index')->with('status', 'Setor excluído com sucesso.');
    }
}
