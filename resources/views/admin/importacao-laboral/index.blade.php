<x-ladmin-panel title="Importação Laboral">
    <x-slot name="contentHeader">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-file-earmark-arrow-up-fill text-primary"></i>
                    Importar Relatório 6-4
                </h3>
                <p class="text-secondary mb-0">Importe dados de remições por trabalho ativas diretamente do iPEN.</p>
            </div>
            <a href="https://www.sc.gov.br/ipen/"
                target="_blank" class="btn btn-info font-weight-bold shadow-sm">
                <i class="bi bi-box-arrow-up-right me-1"></i> Acessar iPEN
            </a>
        </div>
    </x-slot>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="bi bi-info-circle me-2"></i>
                Instruções
            </h5>
        </div>
        <div class="card-body">
            <div class="callout callout-info mb-4">
                <h5>Como proceder:</h5>
                <ol class="mb-0">
                    <li>No iPEN, gere o relatório <b>6-4 (Remições por Trabalho Ativas)</b> da unidade;</li>
                    <li>Pressione <b>Ctrl + A</b> para selecionar todo o texto do relatório;</li>
                    <li>Pressione <b>Ctrl + C</b> para copiar o conteúdo selecionado;</li>
                    <li>Volte a esta tela e pressione <b>Ctrl + V</b> no campo abaixo;</li>
                    <li>Clique em <b>Iniciar Processamento</b> para importar.</li>
                </ol>
                <p class="mb-0 mt-2">
                    O importador vincula o interno via <b>IPEN</b> e grava estabelecimento, tipo de remição,
                    período de remição/liberação e dias da semana.
                </p>
            </div>

            <form id="import-form-64" action="{{ route('admin.importacao.laboral.processar') }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                    <label for="report_data" class="form-label fw-bold">Conteúdo do Relatório 6-4:</label>
                    <textarea class="form-control font-monospace" name="report_data" id="report_data" rows="12" required
                                placeholder="Cole o texto completo do relatório aqui..."
                                style="background-color: #0c0e10; color: #4ade80; border: 1px solid #333; font-size: 0.8rem;"></textarea>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg" id="btn-processar">
                        <i class="bi bi-arrow-repeat me-1"></i> Iniciar Processamento
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-ladmin-panel>

{{-- Modal de Resultado --}}
<div class="modal fade" id="modalResultado64" tabindex="-1" aria-labelledby="modalResultado64Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-check-circle me-2"></i>
                    Importação Concluída
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <h1 class="display-4 fw-bold text-success" id="res64-total">0</h1>
                <p class="text-muted text-uppercase small fw-bold">Registros Processados</p>
                <hr>
                <div class="row">
                    <div class="col-6 border-end">
                        <h4 class="text-primary" id="res64-encontrados">0</h4>
                        <small class="text-uppercase small">Internos Encontrados</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-warning" id="res64-nao-encontrados">0</h4>
                        <small class="text-uppercase small">Não Encontrados</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">FECHAR</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.font-monospace {
    font-family: 'Courier New', Courier, monospace;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('import-form-64');
    const btnProcessar = document.getElementById('btn-processar');
    const modalResultado = new bootstrap.Modal(document.getElementById('modalResultado64'));

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Desabilitar botão e mostrar loading
        btnProcessar.disabled = true;
        btnProcessar.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i> <span class="spinner-border spinner-border-sm" role="status"></span> Processando...';

        try {
            const formData = new FormData(form);
            const response = await fetch('{{ route('admin.importacao.laboral.processar') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                }
            });

            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`Erro HTTP: ${response.status} - ${errorText}`);
            }

            const result = await response.json();

            if (result.success) {
                // Atualizar modal com resultados
                document.getElementById('res64-total').textContent = result.total;
                document.getElementById('res64-encontrados').textContent = result.encontrados;
                document.getElementById('res64-nao-encontrados').textContent = result.nao_encontrados;

                // Mostrar modal
                modalResultado.show();

                // Limpar formulário
                form.reset();

                // Mostrar notificação de sucesso
                if (typeof toastr !== 'undefined') {
                    toastr.success('Importação realizada com sucesso!');
                }
            } else {
                throw new Error(result.message || 'Erro desconhecido');
            }
        } catch (error) {
            console.error('Erro na requisição:', error);

            // Mostrar notificação de erro
            if (typeof toastr !== 'undefined') {
                toastr.error('Erro no processamento: ' + error.message);
            } else {
                alert('Erro no processamento: ' + error.message);
            }
        } finally {
            // Restaurar botão
            btnProcessar.disabled = false;
            btnProcessar.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i> Iniciar Processamento';
        }
    });

    // Auto-resize do textarea
    const textarea = document.getElementById('report_data');
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
});
</script>
@endpush
