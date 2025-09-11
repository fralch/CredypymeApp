<?php

namespace App\Models\Creditos\Clientes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;

class AlbumFoto extends Model
{
    use HasFactory;
    protected $table = 'cliente_album_fotos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'cliente_id',
        'categoria_id',
        'descripcion',
        'comentario',
        'imagen',
        'datos_creacion',

        'created_at',
        'updated_at'
    ];

    protected $casts = [
        "created_at" => "datetime",
    ];

    public function createdAt(): Attribute
    {
        return Attribute::get(fn($value) => Carbon::parse($value)->setTimezone('America/Lima')->format("Y-m-d H:i:s"));
    }
}
