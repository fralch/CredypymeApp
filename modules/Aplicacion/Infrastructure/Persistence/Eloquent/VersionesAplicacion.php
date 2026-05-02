<?php

namespace Modules\Aplicacion\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VersionesAplicacion extends Model
{
    use HasFactory;
    protected $table = 'versiones_aplicacion';
    protected $primaryKey = 'id';

    protected $fillable = [
        'numero_version',
        'observaciones',
        'promo'
    ];

    public $timestamps = true;
}
