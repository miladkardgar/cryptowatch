<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class cryptoUser extends Model
{
    //
    protected $guarded = [];
    use SoftDeletes;
}
