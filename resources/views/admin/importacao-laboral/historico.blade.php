<x-ladmin-panel title="Histórico de Importação Laboral">
    <x-slot name="contentHeader">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-clock-history text-primary"></i>
                    Histórico de Importação 6-4
                </h3>
                <p class="text-secondary mb-0">Acompanhe todas as importações de remições por trabalho realizadas.</p>
            </div>
            <a href="{{ route('admin.importacao.laboral.index') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-file-earmark-arrow-up me-1"></i> Nova Importação
            </a>
        </div>
    </x-slot>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            @if($historico->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Data/Hora</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Novos</th>
                                <th class="text-center">Atualizados</th>
                                <th class="text-center">Inativados</th>
                                <th class="text-center">Encontrados</th>
                                <th class="text-center">Não Encontrados</th>
                                <th>Importado Por</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($historico as $registro)
                                <tr>
                                    <td>{{ $registro->data_importacao->format('d/m/Y H:i') }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $registro->total_importados }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success">{{ $registro->registros_novos }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $registro->registros_atualizados }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger">{{ $registro->registros_inativados }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $registro->internos_encontrados }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning text-dark">{{ $registro->internos_nao_encontrados }}</span>
                                    </td>
                                    <td>{{ $registro->usuarioImportador->name ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.importacao.laboral.detalhes', $registro->id) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="Ver detalhes">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $historico->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <h5 class="mt-3 text-muted">Nenhuma importação registrada</h5>
                    <p class="text-muted">As importações de remições por trabalho aparecerão aqui.</p>
                </div>
            @endif
        </div>
    </div>
</x-ladmin-panel>
