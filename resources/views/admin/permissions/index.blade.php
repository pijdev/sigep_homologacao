<x-ladmin-panel title="Recursos ACL">
    <x-slot name="contentHeader">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-sliders2-vertical text-primary"></i>
                    Catálogo de Recursos ACL
                </h3>
                <p class="text-secondary mb-0">Cadastre menus, módulos, páginas e ações do modelo de autorização.</p>
            </div>
            <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>
                Novo recurso
            </a>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="alert alert-info shadow-sm">{{ session('status') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Código</th>
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th>Setor</th>
                        <th>Pai</th>
                        <th>Status</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($permissoes as $permissao)
                        <tr>
                            <td><code>{{ $permissao->codigo }}</code></td>
                            <td>
                                <div class="fw-semibold">{{ $permissao->nome }}</div>
                                <div class="small text-secondary">{{ $permissao->descricao ?: $permissao->slug }}</div>
                            </td>
                            <td class="text-capitalize">{{ $permissao->tipo }}</td>
                            <td>{{ $permissao->setor?->nome ?? 'Global' }}</td>
                            <td>{{ $permissao->parent?->codigo ?? 'Raiz' }}</td>
                            <td>
                                <span class="badge {{ $permissao->ativo ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $permissao->ativo ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.permissions.edit', $permissao) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                                <form method="POST" action="{{ route('admin.permissions.destroy', $permissao) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Excluir este recurso ACL?')">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-secondary">Nenhum recurso ACL cadastrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($permissoes->hasPages())
            <div class="card-footer bg-white d-flex justify-content-center">
                {{ $permissoes->links() }}
            </div>
        @endif
    </div>
</x-ladmin-panel>
