<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = [
        'departamento',
    ];
}
