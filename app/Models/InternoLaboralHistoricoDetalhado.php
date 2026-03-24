<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InternoLaboralHistoricoDetalhado extends Model
{
    protected $table = 'internos_laboral_historico_detalhado';

    protected $fillable = [
        'id_historico',
        'id_interno_laboral',
        'ipen',
        'campo',
        'valor_antigo',
        'valor_novo',
        'data_alteracao',
        'operacao',
        'alterado_por',
    ];

    protected $casts = [
        'data_alteracao' => 'datetime',
    ];

    public function historico(): BelongsTo
    {
        return $this->belongsTo(InternoLaboralHistorico::class, 'id_historico');
    }

    public function internoLaboral(): BelongsTo
    {
        return $this->belongsTo(InternoLaboral::class, 'id_interno_laboral');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'alterado_por');
    }
}
