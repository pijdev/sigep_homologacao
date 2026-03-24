<x-ladmin-panel title="Detalhes da Importação">
    <x-slot name="contentHeader">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-file-earmark-text text-primary"></i>
                    Detalhes da Importação
                </h3>
                <p class="text-secondary mb-0">Visualize os detalhes da importação realizada em {{ $importacao->data_importacao->format('d/m/Y H:i') }}.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.importacao.laboral.historico') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Voltar ao Histórico
                </a>
                <a href="{{ route('admin.importacao.laboral.index') }}" class="btn btn-primary">
                    <i class="bi bi-file-earmark-arrow-up me-1"></i> Nova Importação
                </a>
            </div>
        </div>
    </x-slot>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Resumo da Importação</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <small class="text-muted d-block">Data/Hora</small>
                            <strong>{{ $importacao->data_importacao->format('d/m/Y H:i:s') }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Importado Por</small>
                            <strong>{{ $importacao->usuarioImportador->name ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Total Importados</small>
                            <strong class="text-secondary">{{ $importacao->total_importados }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Internos Encontrados</small>
                            <strong class="text-primary">{{ $importacao->internos_encontrados }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Internos Não Encontrados</small>
                            <strong class="text-warning">{{ $importacao->internos_nao_encontrados }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Resultados</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-4">
                            <div class="text-center p-3 bg-success bg-opacity-10 rounded">
                                <i class="bi bi-plus-circle display-6 text-success"></i>
                                <h4 class="mt-2 mb-0 text-success">{{ $importacao->registros_novos }}</h4>
                                <small class="text-muted">Novos</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center p-3 bg-info bg-opacity-10 rounded">
                                <i class="bi bi-pencil-square display-6 text-info"></i>
                                <h4 class="mt-2 mb-0 text-info">{{ $importacao->registros_atualizados }}</h4>
                                <small class="text-muted">Atualizados</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center p-3 bg-danger bg-opacity-10 rounded">
                                <i class="bi bi-dash-circle display-6 text-danger"></i>
                                <h4 class="mt-2 mb-0 text-danger">{{ $importacao->registros_inativados }}</h4>
                                <small class="text-muted">Inativados</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Movimentações Detalhadas</h5>
        </div>
        <div class="card-body">
            @if($importacao->detalhes && $importacao->detalhes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>IPEN</th>
                                <th>Campo</th>
                                <th>Operação</th>
                                <th>Valor Antigo</th>
                                <th>Valor Novo</th>
                                <th>Data Alteração</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($importacao->detalhes->take(50) as $detalhe)
                                <tr>
                                    <td><strong>{{ $detalhe->ipen }}</strong></td>
                                    <td>{{ $detalhe->campo }}</td>
                                    <td>
                                        @php
                                            $badgeClass = match($detalhe->operacao) {
                                                'INSERIDO' => 'success',
                                                'ATUALIZADO' => 'info',
                                                'INATIVADO' => 'danger',
                                                'REATIVADO' => 'warning',
                                                'EXCLUIDO' => 'secondary',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $badgeClass }}">{{ $detalhe->operacao }}</span>
                                    </td>
                                    <td class="font-monospace small">{{ $detalhe->valor_antigo ?? '-' }}</td>
                                    <td class="font-monospace small">{{ $detalhe->valor_novo ?? '-' }}</td>
                                    <td>{{ $detalhe->data_alteracao->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if($importacao->detalhes->count() > 50)
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            Exibindo 50 de {{ $importacao->detalhes->count() }} movimentações.
                        </div>
                    @endif
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-inbox display-4 text-muted"></i>
                    <p class="text-muted mt-2 mb-0">Nenhum detalhe registrado para esta importação.</p>
                </div>
            @endif
        </div>
    </div>
</x-ladmin-panel>
