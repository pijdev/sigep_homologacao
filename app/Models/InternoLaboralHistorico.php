<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InternoLaboralHistorico extends Model
{
    protected $table = 'internos_laboral_historico';

    protected $fillable = [
        'data_importacao',
        'total_importados',
        'registros_novos',
        'registros_atualizados',
        'registros_inativados',
        'internos_encontrados',
        'internos_nao_encontrados',
        'importado_por',
    ];

    protected $casts = [
        'data_importacao' => 'datetime',
    ];

    public function usuarioImportador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'importado_por');
    }

    public function detalhes(): HasMany
    {
        return $this->hasMany(InternoLaboralHistoricoDetalhado::class, 'id_historico');
    }
}
