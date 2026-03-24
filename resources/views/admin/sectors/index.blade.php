<x-ladmin-panel title="Setores">
    <x-slot name="contentHeader">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-diagram-3-fill text-primary"></i>
                    Cadastro de Setores
                </h3>
                <p class="text-secondary mb-0">Estruture os setores que sustentam o ACL estadual.</p>
            </div>
            <a href="{{ route('admin.sectors.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>
                Novo setor
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
                        <th>Nome</th>
                        <th>Slug</th>
                        <th>Status</th>
                        <th>Usuários</th>
                        <th>Recursos ACL</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($setores as $setor)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $setor->nome }}</div>
                                <div class="small text-secondary">{{ $setor->descricao ?: 'Sem descrição' }}</div>
                            </td>
                            <td><code>{{ $setor->slug }}</code></td>
                            <td>
                                <span class="badge {{ $setor->ativo ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $setor->ativo ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td>{{ $setor->usuarios_count }}</td>
                            <td>{{ $setor->permissoes_count }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.sectors.edit', $setor) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                                <form method="POST" action="{{ route('admin.sectors.destroy', $setor) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Excluir este setor?')">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-secondary">Nenhum setor cadastrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($setores->hasPages())
            <div class="card-footer bg-white d-flex justify-content-center">
                {{ $setores->links() }}
            </div>
        @endif
    </div>
</x-ladmin-panel>
