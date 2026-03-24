<x-ladmin-panel title="Usuários">
    <x-slot name="contentHeader">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-people-fill text-primary"></i>
                    Administração de Usuários
                </h3>
                <p class="text-secondary mb-0">Gerencie contas, setores, unidades e permissões ACL.</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus-fill me-1"></i>
                Novo usuário
            </a>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="alert alert-success shadow-sm">
            {{ session('status') }}
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nome</th>
                            <th>Login</th>
                            <th>Setor</th>
                            <th>Status</th>
                            <th>Perfil</th>
                            <th>Unidades</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $user->name }}</div>
                                    <div class="text-secondary small">{{ $user->email }}</div>
                                </td>
                                <td>{{ $user->login }}</td>
                                <td>{{ $user->setor?->nome ?? 'Sem setor' }}</td>
                                <td>
                                    @if ($user->is_active)
                                        <span class="badge text-bg-success">Ativo</span>
                                    @else
                                        <span class="badge text-bg-secondary">Inativo</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($user->is_system_admin)
                                        <span class="badge text-bg-danger">Admin do Sistema</span>
                                    @else
                                        <span class="badge text-bg-info">Usuário / Setorial</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach ($user->unidades as $unidade)
                                            <span class="badge text-bg-light border">{{ $unidade->id }} - {{ $unidade->nome }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil-square me-1"></i>
                                        Editar
                                    </a>
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Excluir este usuário? Esta ação não poderá ser desfeita.')"
                                        >
                                            <i class="bi bi-trash me-1"></i>
                                            Excluir
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-secondary">
                                    Nenhum usuário encontrado para o seu escopo.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($users->hasPages())
            <div class="card-footer bg-white d-flex justify-content-center">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</x-ladmin-panel>
