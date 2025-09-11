<?php

namespace App\Models\Creditos\Credito;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cronograma extends Model
{
    use HasFactory;
    protected $table = 'credito_cronogramas';
    protected $primaryKey = 'id';

    protected $fillable = [
        'credito_id',
        'numero_cuota',
        'fecha_vencimiento',
        'cuota',
        'capital',
        'interes',
        'created_at',
        'updated_at'
    ];
}
