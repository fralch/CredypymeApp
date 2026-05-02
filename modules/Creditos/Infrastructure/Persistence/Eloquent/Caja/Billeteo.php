<?php

namespace Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billeteo extends Model
{
    use HasFactory;
    protected $table = 'caja_billeteos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'caja_id',
        '1_cent',
        '10_cent',
        '20_cent',
        '50_cent',
        '1_sol',
        '2_sol',
        '5_sol',
        '10_sol',
        '20_sol',
        '50_sol',
        '100_sol',
        '200_sol',
        
        'datos_creacion',
        'datos_actualizacion',

        'created_at',
        'updated_at'
    ];

    public $timestamps = true;
}
