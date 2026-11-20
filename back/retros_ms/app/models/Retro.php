<?php

namespace app\models;

use Illuminate\Database\Eloquent\Model;

class Sprint extends Model
{
    protected $table = 'retro_items';
    public $timestamps = false;
    protected $hidden = ['password'];
}