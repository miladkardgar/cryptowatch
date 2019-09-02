<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class users_coin extends Model
{
    //
    use SoftDeletes;
    protected $guarded=[];
}
