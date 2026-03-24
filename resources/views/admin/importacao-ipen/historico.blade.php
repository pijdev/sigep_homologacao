<x-ladmin-panel title="Histórico de Importações">
    <x-slot name="contentHeader">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-clock-history text-primary"></i>
                    Histórico de Importações iPEN
                </h3>
                <p class="text-secondary mb-0">Consulte o registro de todas as importações realizadas.</p>
            </div>
            <a href="{{ route('admin.importacao.ipen.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Voltar para Importação
            </a>
        </div>
    </x-slot>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            @if($historico->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Data/Hora</th>
                                <th>Usuário</th>
                                <th>Novos</th>
                                <th>Atualizados</th>
                                <th>Inativados</th>
                                <th>Total</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($historico as $item)
                                <tr>
                                    <td>{{ $item->data_importacao->format('d/m/Y H:i:s') }}</td>
                                    <td>{{ $item->user->name ?? '-' }}</td>
                                    <td>
                                        <span class="badge text-bg-success">{{ $item->registros_novos }}</span>
                                    </td>
                                    <td>
                                        <span class="badge text-bg-info">{{ $item->registros_atualizados }}</span>
                                    </td>
                                    <td>
                                        <span class="badge text-bg-danger">{{ $item->registros_inativados }}</span>
                                    </td>
                                    <td>
                                        <span class="badge text-bg-primary">{{ $item->total_importados }}</span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                onclick="verDetalhes({{ $item->id }})">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-4 text-muted"></i>
                    <p class="text-muted">Nenhuma importação encontrada.</p>
                </div>
            @endif
        </div>
        @if ($historico->hasPages())
            <div class="card-footer bg-white d-flex justify-content-center">
                {{ $historico->links() }}
            </div>
        @endif
    </div>
</x-ladmin-panel>

{{-- Modal de Detalhes --}}
<div class="modal fade" id="modalDetalhes" tabindex="-1" aria-labelledby="modalDetalhesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-info-circle me-2"></i>
                    Detalhes da Importação #<span id="detalhe-id"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="detalhes-conteudo">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">FECHAR</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function verDetalhes(importacaoId) {
    document.getElementById('detalhe-id').textContent = importacaoId;
    document.getElementById('detalhes-conteudo').innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Carregando...</span>
            </div>
        </div>
    `;

    const modal = new bootstrap.Modal(document.getElementById('modalDetalhes'));
    modal.show();

    fetch(`/admin/importacao-ipen/detalhes/${importacaoId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let html = `
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6>Dados Brutos</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>ID Importação:</strong> ${data.importacao.id}<br>
                                        <strong>Data/Hora:</strong> ${data.importacao.data_hora}<br>
                                        <strong>Usuário:</strong> ${data.importacao.user_name}<br>
                                        <strong>Total Linhas:</strong> ${data.importacao.total_linhas}<br>
                                        <strong>Linhas Reconhecidas:</strong> ${data.importacao.linhas_reconhecidas}<br>
                                        <strong>Registros Válidos:</strong> ${data.importacao.registros_validos}
                                    </div>
                                    <div class="mb-3">
                                        <strong>Dados Brutos:</strong>
                                        <textarea class="form-control font-monospace" rows="10" readonly>${data.importacao.dados_brutos}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6>IPENs Extraídos</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>Total de IPENs:</strong> ${data.importacao.ipens_extraidos ? data.importacao.ipens_extraidos.length : 0}
                                    </div>
                                    <div class="mb-3">
                                        <pre class="bg-light p-2 rounded" style="max-height: 200px; overflow-y: auto;">${JSON.stringify(data.importacao.ipens_extraidos || [], null, 2)}</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                document.getElementById('detalhes-conteudo').innerHTML = html;
            } else {
                document.getElementById('detalhes-conteudo').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Erro ao carregar detalhes: ${data.message}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            document.getElementById('detalhes-conteudo').innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Erro na requisição: ${error.message}
                </div>
            `;
        });
}
</script>
@endpush
