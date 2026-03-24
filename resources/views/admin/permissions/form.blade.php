<x-ladmin-panel title="{{ $editing ? 'Editar Recurso ACL' : 'Novo Recurso ACL' }}">
    <x-slot name="contentHeader">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-sliders text-primary"></i>
                    {{ $editing ? 'Editar recurso ACL' : 'Cadastrar recurso ACL' }}
                </h3>
                <p class="text-secondary mb-0">Modele a hierarquia de menus, módulos, páginas e ações.</p>
            </div>
            <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary">Voltar</a>
        </div>
    </x-slot>

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm">{{ $errors->first() }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form method="POST" action="{{ $editing ? route('admin.permissions.update', $permissao) : route('admin.permissions.store') }}">
                @csrf
                @if ($editing)
                    @method('PUT')
                @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Código único</label>
                        <input type="text" name="codigo" class="form-control" value="{{ old('codigo', $permissao->codigo) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nome</label>
                        <input type="text" name="nome" class="form-control" value="{{ old('nome', $permissao->nome) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tipo</label>
                        <select name="tipo" class="form-select" required>
                            @foreach (['menu', 'setor', 'subsetor', 'modulo', 'pagina', 'acao'] as $tipo)
                                <option value="{{ $tipo }}" @selected(old('tipo', $permissao->tipo) === $tipo)>{{ ucfirst($tipo) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" class="form-control" value="{{ old('slug', $permissao->slug) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Ordem</label>
                        <input type="number" name="ordem" class="form-control" value="{{ old('ordem', $permissao->ordem ?? 0) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Setor</label>
                        <select name="setor_id" class="form-select">
                            <option value="">Global</option>
                            @foreach ($setores as $setor)
                                <option value="{{ $setor->id }}" @selected((string) old('setor_id', $permissao->setor_id) === (string) $setor->id)>{{ $setor->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Recurso pai</label>
                        <select name="parent_id" class="form-select">
                            <option value="">Sem pai</option>
                            @foreach ($parents as $parent)
                                <option value="{{ $parent->id }}" @selected((string) old('parent_id', $permissao->parent_id) === (string) $parent->id)>{{ $parent->codigo }} - {{ $parent->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Descrição</label>
                        <textarea name="descricao" class="form-control" rows="3">{{ old('descricao', $permissao->descricao) }}</textarea>
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="ativo" value="1" id="ativo" @checked(old('ativo', $editing ? $permissao->ativo : true))>
                            <label class="form-check-label" for="ativo">Recurso ativo</label>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">{{ $editing ? 'Salvar alterações' : 'Criar recurso' }}</button>
                </div>
            </form>
        </div>
    </div>
</x-ladmin-panel>
