@php
    $editing = $userModel->exists;
@endphp

<x-ladmin-panel title="{{ $editing ? 'Editar Usuário' : 'Novo Usuário' }}">
    <x-slot name="contentHeader">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-person-badge-fill text-primary"></i>
                    {{ $editing ? 'Editar usuário' : 'Cadastrar usuário' }}
                </h3>
                <p class="text-secondary mb-0">Defina setor, unidades e permissões granulares por ACL.</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Voltar
            </a>
        </div>
    </x-slot>

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ $editing ? route('admin.users.update', $userModel) : route('admin.users.store') }}">
        @csrf
        @if ($editing)
            @method('PUT')
        @endif

        <div class="row g-3">
            <div class="col-12 col-lg-8">
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-white">
                        <strong>Dados básicos</strong>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nome</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $userModel->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Login</label>
                                <input type="text" name="login" class="form-control" value="{{ old('login', $userModel->login) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">E-mail</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $userModel->email) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Setor principal</label>
                                <select name="setor_id" class="form-select" required>
                                    <option value="">Selecione</option>
                                    @foreach ($setores as $setor)
                                        <option value="{{ $setor->id }}" @selected((int) old('setor_id', $userModel->setor_id) === $setor->id)>
                                            {{ $setor->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ $editing ? 'Nova senha' : 'Senha' }}</label>
                                <input type="password" name="password" class="form-control" {{ $editing ? '' : 'required' }}>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirmar senha</label>
                                <input type="password" name="password_confirmation" class="form-control" {{ $editing ? '' : 'required' }}>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-white">
                        <strong>Permissões ACL</strong>
                    </div>
                    <div class="card-body">
                        @foreach ($permissions as $group => $groupPermissions)
                            <div class="mb-4">
                                <div class="fw-semibold text-uppercase text-secondary small mb-2">{{ $group }}</div>
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle">
                                        <thead>
                                            <tr>
                                                <th>Recurso</th>
                                                <th>Tipo</th>
                                                <th>Nível</th>
                                                <th>Unidade</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($groupPermissions as $permission)
                                                @php
                                                    $current = $currentPermissions->get($permission->id);
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <div class="fw-semibold">{{ $permission->nome }}</div>
                                                        <div class="small text-secondary">{{ $permission->codigo }}</div>
                                                    </td>
                                                    <td class="text-capitalize">{{ $permission->tipo }}</td>
                                                    <td>
                                                        <select name="permissions[{{ $permission->id }}][nivel]" class="form-select form-select-sm">
                                                            <option value="none">Sem acesso</option>
                                                            <option value="read" @selected(old("permissions.{$permission->id}.nivel", $current->nivel ?? 'none') === 'read')>Read</option>
                                                            <option value="write" @selected(old("permissions.{$permission->id}.nivel", $current->nivel ?? 'none') === 'write')>Write</option>
                                                            @if ($canAssignSystemAdmin)
                                                                <option value="owner" @selected(old("permissions.{$permission->id}.nivel", $current->nivel ?? 'none') === 'owner')>Owner</option>
                                                            @endif
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="permissions[{{ $permission->id }}][unidade_id]" class="form-select form-select-sm">
                                                            <option value="">Todas</option>
                                                            @foreach ($unidades as $unidade)
                                                                <option value="{{ $unidade->id }}" @selected((string) old("permissions.{$permission->id}.unidade_id", $current->unidade_id ?? '') === (string) $unidade->id)>
                                                                    {{ $unidade->id }} - {{ $unidade->nome }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-white">
                        <strong>Configurações</strong>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $editing ? $userModel->is_active : true))>
                            <label class="form-check-label" for="is_active">Usuário ativo</label>
                        </div>

                        @if ($canAssignSystemAdmin)
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="is_system_admin" value="1" id="is_system_admin" @checked(old('is_system_admin', $userModel->is_system_admin))>
                                <label class="form-check-label" for="is_system_admin">Admin do sistema</label>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-white">
                        <strong>Unidades liberadas</strong>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @php
                                $selectedUnidades = collect(old('unidades', $editing ? $userModel->unidades->pluck('id')->all() : []))->map(fn ($item) => (string) $item);
                            @endphp
                            @foreach ($unidades as $unidade)
                                <label class="form-check border rounded px-3 py-2">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="unidades[]"
                                        value="{{ $unidade->id }}"
                                        @checked($selectedUnidades->contains((string) $unidade->id))
                                    >
                                    <span class="form-check-label">
                                        <strong>{{ $unidade->id }}</strong> - {{ $unidade->nome }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check2-circle me-1"></i>
                        {{ $editing ? 'Salvar alterações' : 'Criar usuário' }}
                    </button>
                </div>
            </div>
        </div>
    </form>
</x-ladmin-panel>
