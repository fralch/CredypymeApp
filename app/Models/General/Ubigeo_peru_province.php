<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;

class Ubigeo_peru_province extends Model
{
    protected $primaryKey = 'id';
    protected $casts = [
        'id' => 'string',
    ];
    public $timestamps = false;
    //
}
