<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AclService;
use App\Models\Interno;
use App\Models\InternoLaboral;
use App\Models\InternoLaboralHistorico;
use App\Models\InternoLaboralHistoricoDetalhado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DadosImporta64Controller extends Controller
{
    protected $aclService;

    public function __construct(AclService $aclService)
    {
        $this->aclService = $aclService;
    }

    /**
     * Exibe página principal de importação do Relatório 6-4
     */
    public function index()
    {
        abort_unless(
            $this->aclService->userHasPermission(Auth::user(), 'pagina.importacao_laboral.relatorio_64', 'read'),
            403
        );

        return view('admin.importacao-laboral.index');
    }

    /**
     * Processa dados do relatório 6-4 (Remições por Trabalho)
     */
    public function processar(Request $request)
    {
        abort_unless(
            $this->aclService->userHasPermission(Auth::user(), 'acao.importacao_laboral.processar', 'write'),
            403
        );

        try {
            $rawData = $request->input('report_data');
            if (empty(trim($rawData))) {
                return response()->json(['success' => false, 'message' => 'Nenhum dado informado.'], 400);
            }

            // Parse dos dados do relatório 6-4
            $registros = $this->parseRelatorio64($rawData);
            if (empty($registros)) {
                throw new \Exception('Nenhum registro válido identificado no texto colado.');
            }

            // Extrair IPENs únicos
            $ipens = array_values(array_unique(array_map(fn($r) => (int)$r['ipen'], $registros)));
            
            // Buscar internos existentes
            $internos = [];
            if (!empty($ipens)) {
                $placeholders = implode(',', array_fill(0, count($ipens), '?'));
                $stmtInternos = DB::connection()->getPdo()->prepare(
                    "SELECT ipen, nome, situacao FROM internos WHERE ipen IN ({$placeholders})"
                );
                $stmtInternos->execute($ipens);
                foreach ($stmtInternos->fetchAll() as $row) {
                    $internos[(int)$row['ipen']] = $row;
                }
            }

            $agora = now()->toDateTimeString();
            $usuarioId = Auth::id();
            $total = count(array_unique(array_map(fn($r) => (int)$r['ipen'], $registros)));
            $encontrados = 0;
            $naoEncontrados = 0;
            $novos = 0;
            $atualizados = 0;
            $inativados = 0;

            DB::transaction(function () use ($registros, $internos, $agora, $usuarioId, &$encontrados, &$naoEncontrados, &$novos, &$atualizados, &$inativados) {
                // Criar registro de histórico
                $idHistorico = InternoLaboralHistorico::create([
                    'data_importacao' => now(),
                    'total_importados' => $total,
                    'registros_novos' => 0,
                    'registros_atualizados' => 0,
                    'registros_inativados' => 0,
                    'internos_encontrados' => 0,
                    'internos_nao_encontrados' => 0,
                    'importado_por' => $usuarioId,
                ])->id;

                // Disponibiliza contexto para histórico detalhado
                DB::statement("SET @laboral_import_id = {$idHistorico}");
                DB::statement("SET @laboral_user_id = {$usuarioId}");

                // Buscar ativos atuais
                $ativosNoBanco = InternoLaboral::where('status', 'A')->get(['id', 'ipen']);
                $ativosMap = [];
                foreach ($ativosNoBanco as $ativo) {
                    $ativosMap[$this->laboralKey((int)$ativo['ipen'])] = (int)$ativo['id'];
                }

                $processadosMap = [];

                foreach ($registros as $r) {
                    $ipen = (int)$r['ipen'];
                    $interno = $internos[$ipen] ?? null;
                    $internoEncontrado = $interno ? 1 : 0;

                    if ($internoEncontrado) {
                        $encontrados++;
                    } else {
                        $naoEncontrados++;
                    }

                    $estabelecimento = trim((string)$r['estabelecimento']);
                    $key = $this->laboralKey($ipen);
                    $processadosMap[$key] = true;

                    $existente = InternoLaboral::where('ipen', $ipen)
                        ->where('status', 'A')
                        ->first();

                    if (!$existente) {
                        // Novo registro
                        InternoLaboral::create([
                            'ipen' => $ipen,
                            'estabelecimento' => $estabelecimento,
                            'remicao_inicio' => $r['remicao_inicio'],
                            'remicao_fim' => $r['remicao_fim'],
                            'liberacao_inicio' => $r['liberacao_inicio'],
                            'liberacao_fim' => $r['liberacao_fim'],
                            'dias_semana' => $r['dias_semana'],
                            'dias_semana_json' => $r['dias_semana_json'],
                            'status' => 'A',
                            'data_ativo' => now(),
                            'importado_em' => now(),
                            'importado_por' => $usuarioId,
                        ]);
                        $novos++;
                        continue;
                    }

                    $mudou = false;
                    $reativado = ($existente['status'] === 'I');

                    $comparar = [
                        ['remicao_inicio', $r['remicao_inicio']],
                        ['remicao_fim', $r['remicao_fim']],
                        ['liberacao_inicio', $r['liberacao_inicio']],
                        ['liberacao_fim', $r['liberacao_fim']],
                        ['dias_semana', $r['dias_semana']],
                        ['dias_semana_json', $r['dias_semana_json']],
                    ];

                    foreach ($comparar as [$campo, $valorNovo]) {
                        $valorAtual = $existente[$campo];
                        $valorAtualNorm = $valorAtual === null ? null : (string)$valorAtual;
                        $valorNovoNorm = $valorNovo === null ? null : (string)$valorNovo;
                        if ($valorAtualNorm !== $valorNovoNorm) {
                            $mudou = true;
                            break;
                        }
                    }

                    if ($mudou || $reativado) {
                        $existente->update([
                            'remicao_inicio' => $r['remicao_inicio'],
                            'remicao_fim' => $r['remicao_fim'],
                            'liberacao_inicio' => $r['liberacao_inicio'],
                            'liberacao_fim' => $r['liberacao_fim'],
                            'dias_semana' => $r['dias_semana'],
                            'dias_semana_json' => $r['dias_semana_json'],
                            'data_alterado' => now(),
                            'importado_em' => now(),
                            'importado_por' => $usuarioId,
                        ]);
                        $atualizados++;
                    }
                }

                // Inativar não encontrados
                foreach ($ativosMap as $key => $idAtivo) {
                    if (!isset($processadosMap[$key])) {
                        $afetados = InternoLaboral::where('id', $idAtivo)
                            ->where('status', 'A')
                            ->update([
                                'status' => 'I',
                                'data_inativo' => now(),
                                'data_alterado' => now(),
                                'importado_em' => now(),
                                'importado_por' => $usuarioId,
                            ]);
                        if ($afetados > 0) {
                            $inativados++;
                        }
                    }
                }

                // Atualizar histórico
                $historico = InternoLaboralHistorico::find($idHistorico);
                $historico->update([
                    'registros_novos' => $novos,
                    'registros_atualizados' => $atualizados,
                    'registros_inativados' => $inativados,
                    'internos_encontrados' => $encontrados,
                    'internos_nao_encontrados' => $naoEncontrados,
                ]);

                // Limpar variáveis de sessão
                DB::statement("SET @laboral_import_id = NULL");
                DB::statement("SET @laboral_user_id = NULL");
            });

            return response()->json([
                'success' => true,
                'total' => $total,
                'novos' => $novos,
                'atualizados' => $atualizados,
                'inativados' => $inativados,
                'encontrados' => $encontrados,
                'nao_encontrados' => $naoEncontrados,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro no processamento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Parsing do Relatório 6-4
     */
    private function parseRelatorio64(string $rawData): array
    {
        $normalizado = str_replace(["\r\n", "\r"], "\n", $rawData);
        $linhas = explode("\n", $normalizado);

        $registros = [];
        $estabelecimentoAtual = '';
        $pendente = null;

        foreach ($linhas as $linhaOriginal) {
            $linha = trim(preg_replace('/\s+/u', ' ', $linhaOriginal));
            if ($linha === '') {
                continue;
            }

            // Ignorar linhas de cabeçalho/rodapé
            if (preg_match('/^(www\.|https:|\d{2}\/\d{2}\/\d{4},|Impresso em:)/', $linha)) {
                continue;
            }

            // Extrair estabelecimento
            if (preg_match('/^Estabelecimento:\s*(.+?)\s+\d+\s+detentos$/iu', $linha, $m)) {
                $estabelecimentoAtual = trim($m[1]);
                continue;
            }

            // Extrair IPEN e nome do interno
            if (preg_match('/^(\d{6})\s+(.+)$/u', $linha, $m)) {
                // Verificar se é o mesmo IPEN do registro pendente
                if ($pendente !== null && $pendente['ipen'] === $m[1]) {
                    // Mesmo IPEN - apenas acumular as linhas de detalhe
                } else {
                    // IPEN diferente - finalizar registro anterior
                    if ($pendente !== null) {
                        $registro = $this->buildRemicaoRecord($pendente);
                        if ($registro !== null) {
                            $registros[] = $registro;
                        }
                    }

                    // Iniciar novo registro
                    $pendente = [
                        'ipen' => $m[1],
                        'nome_interno' => $m[2],
                        'estabelecimento' => $estabelecimentoAtual,
                        'linhas_detalhe' => []
                    ];
                }
                continue;
            }

            // Adicionar linhas de detalhes ao registro pendente
            if ($pendente !== null) {
                $pendente['linhas_detalhe'][] = $linha;
            }
        }

        // Finalizar último registro
        if ($pendente !== null) {
            $registro = $this->buildRemicaoRecord($pendente);
            if ($registro !== null) {
                $registros[] = $registro;
            }
        }

        return $registros;
    }

    /**
     * Build do registro de remição
     */
    private function buildRemicaoRecord(array $pendente): ?array
    {
        if (empty($pendente['linhas_detalhe'])) {
            return null;
        }

        $todasAsDatas = [];
        $todosOsDias = [];

        foreach ($pendente['linhas_detalhe'] as $linha) {
            if (preg_match('/\b(TRABALHO|ESTUDO|LEITURA)\b.*\d{2}\/\d{2}\/\d{4}/u', $linha)) {
                $match = preg_match(
                    '/\b(TRABALHO\s+(?:EXTERNO|INTERNO)|ESTUDO\s+[A-ZÀ-Ú\s]+|LEITURA(?:\s+[A-ZÀ-Ú\s]*)?)\s+(\d{2}\/\d{2}\/\d{4})\s+(\d{2}\/\d{2}\/\d{4})\s+(\d{2}\/\d{2}\/\d{4})\s+(\d{2}\/\d{2}\/\d{4})\s+(.+)$/u',
                    $linha,
                    $m
                );

                if ($match) {
                    $todasAsDatas[] = [
                        'remicao_inicio' => $this->formatDateToSql($m[2]),
                        'remicao_fim' => $this->formatDateToSql($m[3]),
                        'liberacao_inicio' => $this->formatDateToSql($m[4]),
                        'liberacao_fim' => $this->formatDateToSql($m[5])
                    ];

                    $parteFinal = trim($m[6]);
                    preg_match_all('/\b(?:D|2ª|3ª|4ª|5ª|6ª|S)\b/u', $parteFinal, $matches);
                    $diasEncontrados = $matches[0] ?? [];
                    $todosOsDias = array_merge($todosOsDias, $diasEncontrados);
                }
            }
        }

        if (empty($todasAsDatas)) {
            return null;
        }

        // Consolidar datas
        $datasInicio = array_filter(array_column($todasAsDatas, 'remicao_inicio'));
        $datasFim = array_filter(array_column($todasAsDatas, 'remicao_fim'));
        $datasLiberacaoInicio = array_filter(array_column($todasAsDatas, 'liberacao_inicio'));
        $datasLiberacaoFim = array_filter(array_column($todasAsDatas, 'liberacao_fim'));

        sort($datasInicio);
        rsort($datasFim);
        sort($datasLiberacaoInicio);
        rsort($datasLiberacaoFim);

        // Consolidar dias únicos em ordem
        $diasUnicos = array_unique($todosOsDias);
        $ordem = ['D', '2ª', '3ª', '4ª', '5ª', '6ª', 'S'];
        $diasOrdenados = [];
        foreach ($ordem as $dia) {
            if (in_array($dia, $diasUnicos)) {
                $diasOrdenados[] = $dia;
            }
        }

        if (empty($diasOrdenados) && !empty($diasUnicos)) {
            $diasOrdenados = array_values($diasUnicos);
        }

        $diasTexto = implode(' ', $diasOrdenados);
        $diasJson = json_encode($diasOrdenados, JSON_UNESCAPED_UNICODE);

        if (empty($pendente['ipen'])) {
            return null;
        }

        return [
            'ipen' => (int)$pendente['ipen'],
            'estabelecimento' => trim((string)($pendente['estabelecimento'] ?? 'NAO INFORMADO')),
            'remicao_inicio' => $datasInicio[0] ?? null,
            'remicao_fim' => $datasFim[0] ?? null,
            'liberacao_inicio' => $datasLiberacaoInicio[0] ?? null,
            'liberacao_fim' => $datasLiberacaoFim[0] ?? null,
            'dias_semana' => $diasTexto,
            'dias_semana_json' => $diasJson,
        ];
    }

    /**
     * Formata data para SQL
     */
    private function formatDateToSql(?string $date): ?string
    {
        if ($date === null || trim($date) === '') {
            return null;
        }

        $dt = \DateTime::createFromFormat('d/m/Y', trim($date));
        if (!$dt) {
            return null;
        }

        return $dt->format('Y-m-d');
    }

    /**
     * Gera chave única para registro laboral
     */
    private function laboralKey(int $ipen): string
    {
        return (string)$ipen;
    }

    /**
     * Exibe página de histórico
     */
    public function historico()
    {
        abort_unless(
            $this->aclService->userHasPermission(Auth::user(), 'pagina.importacao_laboral.relatorio_64', 'read'),
            403
        );

        $historico = InternoLaboralHistorico::with(['usuarioImportador:id,name'])
            ->orderBy('data_importacao', 'desc')
            ->paginate(20);

        return view('admin.importacao-laboral.historico', compact('historico'));
    }

    /**
     * Exibe detalhes de uma importação específica
     */
    public function detalhes($id)
    {
        abort_unless(
            $this->aclService->userHasPermission(Auth::user(), 'pagina.importacao_laboral.relatorio_64', 'read'),
            403
        );

        $importacao = InternoLaboralHistorico::with(['usuarioImportador:id,name', 'detalhes'])
            ->findOrFail($id);

        return view('admin.importacao-laboral.detalhes', compact('importacao'));
    }
}
