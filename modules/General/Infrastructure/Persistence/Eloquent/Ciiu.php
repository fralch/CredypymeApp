<?php

namespace Modules\General\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ciiu extends Model
{
    protected $table = 'ciiu';
    protected $primaryKey = 'id';
    protected $fillable = [
        'codigo',
        'nombre',
        'habilitado',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at',
    ];
    public $timestamps = true;
}
