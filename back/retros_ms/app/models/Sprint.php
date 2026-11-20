<?php

namespace app\models;

use Illuminate\Database\Eloquent\Model;

class Sprint extends Model
{
    protected $table = 'sprints';
    public $timestamps = false;
    protected $hidden = ['password'];
}