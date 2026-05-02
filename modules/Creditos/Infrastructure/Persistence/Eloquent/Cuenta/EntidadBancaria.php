<?php

namespace Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntidadBancaria extends Model
{
    use HasFactory;
    protected $table = 'entidades';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre',
        'habilitado'
    ];

    public $timestamps = true;
}
