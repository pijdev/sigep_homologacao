<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AclService;
use App\Models\Interno;
use App\Models\InternoHistorico;
use App\Models\InternoHistoricoDetalhado;
use App\Models\InternoImportacaoRaw;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ImportacaoIpenController extends Controller
{
    protected $aclService;

    public function __construct(AclService $aclService)
    {
        $this->aclService = $aclService;
    }

    /**
     * Exibe página principal de importação
     */
    public function index()
    {
        abort_unless(
            $this->aclService->userHasPermission(Auth::user(), 'pagina.importacao_ipen.relatorio_18', 'read'),
            403
        );

        return view('admin.importacao-ipen.index');
    }

    /**
     * Processa dados do relatório 1-8
     */
    public function processar(Request $request)
    {
        abort_unless(
            $this->aclService->userHasPermission(Auth::user(), 'acao.importacao_ipen.processar', 'write'),
            403
        );

        try {
            $rawData = $request->input('report_data');
            if (empty(trim($rawData))) {
                return response()->json(['success' => false, 'message' => 'Nenhum dado informado.'], 400);
            }

            // Salvar dados brutos para auditoria
            $importacaoRaw = InternoImportacaoRaw::create([
                'user_id' => Auth::id(),
                'data_hora' => now(),
                'dados_brutos' => $rawData,
                'total_linhas' => substr_count($rawData, "\n") + 1,
            ]);

            // Processar dados (implementar parsing inteligente)
            $dadosProcessados = $this->parseDados($rawData);
            $ipensExtraidos = array_keys($dadosProcessados);

            // Atualizar importação raw com IPENs extraídos
            $importacaoRaw->ipens_extraidos = $ipensExtraidos;
            $importacaoRaw->linhas_reconhecidas = count($ipensExtraidos);
            $importacaoRaw->registros_validos = count($dadosProcessados);
            $importacaoRaw->save();

            // Validar volume mínimo
            $this->validarVolume($dadosProcessados);

            // Processar em transação
            DB::transaction(function () use ($dadosProcessados) {
                $novos = 0;
                $atualizados = 0;
                $inativados = 0;

                foreach ($dadosProcessados as $ipen => $dados) {
                    $interno = Interno::find($ipen);

                    if (!$interno) {
                        // Novo interno
                        Interno::create([
                            'ipen' => $ipen,
                            'nome' => $dados['nome'],
                            'nome_social' => '',
                            'situacao' => $dados['situacao'],
                            'ala' => $dados['ala'],
                            'galeria' => $dados['galeria'],
                            'bloco' => $dados['bloco'],
                            'piso' => $dados['piso'],
                            'tipo_residencia' => $dados['tipo_residencia'],
                            'res' => $dados['res'],
                            'status' => 'A',
                            'data_ativo' => now(),
                            'kit' => 0,
                            'tamanho_kit' => 'G',
                        ]);
                        $novos++;
                    } else {
                        // Interno existente - verificar mudanças
                        $mudouLocal = ($interno->galeria != $dados['galeria'] ||
                            $interno->bloco != $dados['bloco'] ||
                            $interno->res != $dados['res'] ||
                            $interno->ala != $dados['ala']);

                        $mudouSituacao = ($interno->situacao != $dados['situacao']);
                        $reativado = ($interno->status == 'I');

                        if ($mudouLocal || $mudouSituacao || $reativado) {
                            // Registrar mudança no histórico detalhado
                            if ($mudouLocal) {
                                $this->registrarMudanca($ipen, 'galeria', $interno->galeria, $dados['galeria']);
                                $this->registrarMudanca($ipen, 'bloco', $interno->bloco, $dados['bloco']);
                                $this->registrarMudanca($ipen, 'res', $interno->res, $dados['res']);
                                $this->registrarMudanca($ipen, 'ala', $interno->ala, $dados['ala']);
                            }
                            if ($mudouSituacao) {
                                $this->registrarMudanca($ipen, 'situacao', $interno->situacao, $dados['situacao']);
                            }

                            $interno->update([
                                'nome' => $dados['nome'],
                                'situacao' => $dados['situacao'],
                                'ala' => $dados['ala'],
                                'galeria' => $dados['galeria'],
                                'bloco' => $dados['bloco'],
                                'piso' => $dados['piso'],
                                'tipo_residencia' => $dados['tipo_residencia'],
                                'res' => $dados['res'],
                                'status' => 'A',
                                'data_alterado' => now(),
                                'data_inativo' => null,
                            ]);

                            if ($reativado) {
                                $this->registrarMudanca($ipen, 'status', 'I', 'A');
                            }

                            $atualizados++;
                        }
                    }
                }

                // Inativar não encontrados
                $ativosNoBanco = Interno::where('status', 'A')->pluck('ipen')->toArray();
                $naoEncontrados = array_diff($ativosNoBanco, $ipensExtraidos);

                foreach ($naoEncontrados as $ipenInativo) {
                    Interno::where('ipen', $ipenInativo)->update([
                        'status' => 'I',
                        'data_inativo' => now(),
                    ]);
                    $this->registrarMudanca($ipenInativo, 'status', 'A', 'I');
                    $inativados++;
                }

                // Salvar histórico da importação
                InternoHistorico::create([
                    'data_importacao' => now(),
                    'registros_novos' => $novos,
                    'registros_atualizados' => $atualizados,
                    'registros_inativados' => $inativados,
                    'total_importados' => count($dadosProcessados),
                ]);
            });

            return response()->json([
                'success' => true,
                'total' => count($dadosProcessados),
                'novos' => $novos,
                'atualizados' => $atualizados,
                'inativados' => $inativados,
                'importacao_id' => $importacaoRaw->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro no processamento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Parsing inteligente dos dados (extraído do legado)
     */
    private function parseDados($rawData)
    {
        $linhas = explode("\n", $rawData);
        $dadosProcessados = [];
        $ipensProcessados = [];

        // Aceita IPEN com pontuação, hífen ou apenas dígitos
        $extrairIpen = function (string $linha): ?int {
            if (!preg_match('/^\s*([0-9][0-9\.\-]{4,})\b/u', $linha, $m)) {
                return null;
            }
            $apen = preg_replace('/\D+/', '', $m[1]);
            if ($apen === '' || strlen($apen) < 5) {
                return null;
            }
            return (int)$apen;
        };

        foreach ($linhas as $linha) {
            $linhaLimpa = trim($linha);
            if ($linhaLimpa === '') continue;

            $ipen = $extrairIpen($linhaLimpa);
            if ($ipen === null) continue;

            $ipensProcessados[] = $ipen;

            // Remover IPEN do início
            $textoDados = trim(preg_replace('/^\s*[0-9][0-9\.\-]{4,}\s*(?:-\s*)?/u', '', $linhaLimpa));

            $nomeFinal = '';
            $situacaoFinal = '';
            $ala = '';
            $galeria = '';
            $bloco = '';
            $piso = 0;
            $tipo_residencia = '';
            $res = null;

            // Estratégia 1: Tabulação
            if (strpos($textoDados, "\t") !== false) {
                $cols = explode("\t", $textoDados);
                $cols = array_map('trim', $cols);
                $cols = array_values(array_filter($cols));

                $qtdCols = count($cols);
                if ($qtdCols >= 5) {
                    $nomeFinal = $cols[0];
                    $situacaoFinal = $cols[1];

                    $last = $qtdCols - 1;
                    $resIndex = is_numeric($cols[$last]) ? $last : -1;

                    if ($resIndex > 0) {
                        $res = (int)$cols[$resIndex];
                        $tipo_residencia = $cols[$resIndex - 1];
                        $piso = (int)$cols[$resIndex - 2];
                        $bloco = $cols[$resIndex - 3];
                        $galeria = $cols[$resIndex - 4];
                        $ala = $cols[$resIndex - 5];
                    }
                }
            }

            // Estratégia 2: Fluxo de texto
            if (empty($nomeFinal)) {
                $partes = preg_split('/\s+/', $textoDados);
                $qtdPartes = count($partes);

                $resIndex = is_numeric(end($partes)) ? $qtdPartes - 1 : $qtdPartes;
                $pisoIndex = -1;

                for ($k = $resIndex - 1; $k >= max(0, $resIndex - 7); $k--) {
                    if (is_numeric($partes[$k]) && strlen($partes[$k]) <= 2 && ($k - 3) >= 0) {
                        $pisoIndex = $k;
                        break;
                    }
                }

                if ($pisoIndex !== -1) {
                    $ala = $partes[$pisoIndex - 3];
                    $galeria = $partes[$pisoIndex - 2];
                    $bloco = $partes[$pisoIndex - 1];
                    $piso = (int)$partes[$pisoIndex];
                    $res = isset($partes[$resIndex]) && $resIndex < $qtdPartes ? (int)$partes[$resIndex] : null;

                    $tipoParts = [];
                    for ($j = $pisoIndex + 1; $j < $resIndex; $j++) {
                        $tipoParts[] = $partes[$j];
                    }
                    $tipo_residencia = implode(' ', $tipoParts);

                    $textoMeio = implode(' ', array_slice($partes, 0, $pisoIndex - 3));
                    $nomeFinal = $textoMeio;
                    $situacaoFinal = "VERIFICAR (NOVO)";
                }
            }

            if (!empty($nomeFinal)) {
                $dadosProcessados[$ipen] = [
                    'nome' => $nomeFinal,
                    'situacao' => $situacaoFinal,
                    'ala' => $ala,
                    'galeria' => $galeria,
                    'bloco' => $bloco,
                    'piso' => $piso,
                    'tipo_residencia' => $tipo_residencia,
                    'res' => $res,
                ];
            }
        }

        return $dadosProcessados;
    }

    /**
     * Valida volume mínimo de importação
     */
    private function validarVolume($dadosProcessados)
    {
        $ativosAtuais = Interno::where('status', 'A')->count();
        $historicoReferencia = InternoHistorico::where('total_importados', '>=', 100)
            ->max('total_importados') ?? 0;
        $baseComparacao = max($ativosAtuais, $historicoReferencia);

        if ($baseComparacao > 0) {
            $minimoSeguro = (int)floor($baseComparacao * 0.70);
            if (count($dadosProcessados) < $minimoSeguro) {
                throw new \Exception(
                    "Importação bloqueada por segurança: " . count($dadosProcessados) .
                        " registros válidos para uma base esperada de {$baseComparacao}. " .
                        "Isso indica possível mudança no formato do relatório."
                );
            }
        }
    }

    /**
     * Registra mudança no histórico detalhado
     */
    private function registrarMudanca($ipen, $campo, $valorAntigo, $valorNovo)
    {
        InternoHistoricoDetalhado::create([
            'ipen' => $ipen,
            'campo' => $campo,
            'valor_antigo' => $valorAntigo,
            'valor_novo' => $valorNovo,
            'data_alteracao' => now(),
            'operacao' => $valorAntigo === null ? 'INSERIDO' : 'ATUALIZADO',
        ]);
    }

    /**
     * Exibe página de histórico
     */
    public function historico()
    {
        abort_unless(
            $this->aclService->userHasPermission(Auth::user(), 'pagina.importacao_ipen.relatorio_18', 'read'),
            403
        );

        $historico = InternoHistorico::with(['user:id,name'])
            ->orderBy('data_importacao', 'desc')
            ->paginate(20);

        return view('admin.importacao-ipen.historico', compact('historico'));
    }

    /**
     * Exibe detalhes de uma importação específica
     */
    public function detalhes($id)
    {
        abort_unless(
            $this->aclService->userHasPermission(Auth::user(), 'pagina.importacao_ipen.relatorio_18', 'read'),
            403
        );

        $importacao = InternoImportacaoRaw::with('user:id,name')
            ->findOrFail($id);

        return view('admin.importacao-ipen.detalhes', compact('importacao'));
    }
}
