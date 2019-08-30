<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class crypto_user extends Model
{
    //
    use SoftDeletes;
    protected $guarded=[];
}
