<?php

namespace Modules\General\Infrastructure\Persistence\Eloquent;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'usuarios';
    protected $primaryKey = 'dni';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'dni',
        'usuario',
        'clave',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'agencia_id',
        'cargo_id',
        'habilitado',
    ];

    protected $hidden = [
        'clave',
    ];

    public function getAuthPassword(): string
    {
        return (string) $this->clave;
    }
}

