<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InternoLaboral extends Model
{
    protected $table = 'internos_laboral';

    protected $fillable = [
        'ipen',
        'estabelecimento',
        'remicao_inicio',
        'remicao_fim',
        'liberacao_inicio',
        'liberacao_fim',
        'dias_semana',
        'dias_semana_json',
        'status',
        'data_ativo',
        'data_alterado',
        'data_inativo',
        'importado_em',
        'importado_por',
    ];

    protected $casts = [
        'remicao_inicio' => 'date',
        'remicao_fim' => 'date',
        'liberacao_inicio' => 'date',
        'liberacao_fim' => 'date',
        'dias_semana_json' => 'array',
        'data_ativo' => 'datetime',
        'data_alterado' => 'datetime',
        'data_inativo' => 'datetime',
        'importado_em' => 'datetime',
    ];

    public function interno(): BelongsTo
    {
        return $this->belongsTo(Interno::class, 'ipen', 'ipen');
    }

    public function usuarioImportador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'importado_por');
    }

    public function historicoDetalhado(): HasMany
    {
        return $this->hasMany(InternoLaboralHistoricoDetalhado::class, 'id_interno_laboral');
    }
}
