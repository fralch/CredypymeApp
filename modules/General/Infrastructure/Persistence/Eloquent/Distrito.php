<?php

namespace Modules\General\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distrito extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = [
        'distrito',
        'provincia_id',
    ];
}
