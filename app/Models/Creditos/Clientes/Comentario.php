<?php

namespace App\Models\Creditos\Clientes;

use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    protected $table = 'cliente_comentarios';
    protected $primaryKey = 'id';
    protected $fillable = [
        'cliente_id',
        'motivo',
        'comentario',
        'fecha_comentario',
        'datos_creacion',
        'created_at',
        'updated_at',
    ];
    public $timestamps = true;

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'id');
    }
}
