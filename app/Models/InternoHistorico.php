<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternoHistorico extends Model
{
    protected $table = 'internos_historico';

    protected $fillable = [
        'data_importacao',
        'registros_novos',
        'registros_atualizados',
        'registros_inativados',
        'total_importados',
    ];

    protected $casts = [
        'data_importacao' => 'datetime',
    ];
}