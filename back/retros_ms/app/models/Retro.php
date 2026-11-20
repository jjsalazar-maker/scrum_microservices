<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Retro extends Model
{
    protected $table = 'retro_items';
    public $timestamps = false;

    protected $fillable = [
        'sprint_id',
        'categoria',
        'descripcion',
        'cumplida',
        'fecha_revision',
    ];

    protected $casts = [
        'sprint_id' => 'integer',
        'cumplida' => 'boolean',
    ];
}
