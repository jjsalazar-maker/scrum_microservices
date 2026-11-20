<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sprint extends Model
{
    protected $table = 'sprints';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'fecha_revision',
    ];

    protected $casts = [
        'id' => 'integer',
    ];
}