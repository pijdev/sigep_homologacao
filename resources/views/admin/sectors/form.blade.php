<x-ladmin-panel title="{{ $editing ? 'Editar Setor' : 'Novo Setor' }}">
    <x-slot name="contentHeader">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-diagram-3 text-primary"></i>
                    {{ $editing ? 'Editar setor' : 'Cadastrar setor' }}
                </h3>
                <p class="text-secondary mb-0">Mantenha a taxonomia institucional do sistema.</p>
            </div>
            <a href="{{ route('admin.sectors.index') }}" class="btn btn-outline-secondary">Voltar</a>
        </div>
    </x-slot>

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm">{{ $errors->first() }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form method="POST" action="{{ $editing ? route('admin.sectors.update', $setor) : route('admin.sectors.store') }}">
                @csrf
                @if ($editing)
                    @method('PUT')
                @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nome</label>
                        <input type="text" name="nome" class="form-control" value="{{ old('nome', $setor->nome) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" class="form-control" value="{{ old('slug', $setor->slug) }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Descrição</label>
                        <textarea name="descricao" class="form-control" rows="3">{{ old('descricao', $setor->descricao) }}</textarea>
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="ativo" value="1" id="ativo" @checked(old('ativo', $editing ? $setor->ativo : true))>
                            <label class="form-check-label" for="ativo">Setor ativo</label>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">{{ $editing ? 'Salvar alterações' : 'Criar setor' }}</button>
                </div>
            </form>
        </div>
    </div>
</x-ladmin-panel>
