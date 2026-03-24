<x-ladmin-panel title="Unidades">
    <x-slot name="contentHeader">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-building-fill-gear text-primary"></i>
                    Cadastro de Unidades
                </h3>
                <p class="text-secondary mb-0">Mantenha a base de unidades prisionais acessíveis no sistema.</p>
            </div>
            <a href="{{ route('admin.units.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>
                Nova unidade
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
                        <th>Usuários vinculados</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($unidades as $unidade)
                        <tr>
                            <td><code>{{ $unidade->id }}</code></td>
                            <td>{{ $unidade->nome }}</td>
                            <td>{{ $unidade->usuarios_count }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.units.edit', $unidade) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                                <form method="POST" action="{{ route('admin.units.destroy', $unidade) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Excluir esta unidade?')">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-secondary">Nenhuma unidade cadastrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($unidades->hasPages())
            <div class="card-footer bg-white d-flex justify-content-center">
                {{ $unidades->links() }}
            </div>
        @endif
    </div>
</x-ladmin-panel>
