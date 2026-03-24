<x-ladmin-panel title="{{ $editing ? 'Editar Unidade' : 'Nova Unidade' }}">
    <x-slot name="contentHeader">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-building text-primary"></i>
                    {{ $editing ? 'Editar unidade' : 'Cadastrar unidade' }}
                </h3>
                <p class="text-secondary mb-0">O código da unidade é fixo após a criação para preservar vínculos.</p>
            </div>
            <a href="{{ route('admin.units.index') }}" class="btn btn-outline-secondary">Voltar</a>
        </div>
    </x-slot>

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm">{{ $errors->first() }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form method="POST" action="{{ $editing ? route('admin.units.update', $unidade) : route('admin.units.store') }}">
                @csrf
                @if ($editing)
                    @method('PUT')
                @endif

                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Código</label>
                        <input type="number" name="id" class="form-control" value="{{ old('id', $unidade->id) }}" {{ $editing ? 'disabled' : 'required' }}>
                    </div>
                    <div class="col-md-9">
                        <label class="form-label">Nome</label>
                        <input type="text" name="nome" class="form-control" value="{{ old('nome', $unidade->nome) }}" required>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">{{ $editing ? 'Salvar alterações' : 'Criar unidade' }}</button>
                </div>
            </form>
        </div>
    </div>
</x-ladmin-panel>
