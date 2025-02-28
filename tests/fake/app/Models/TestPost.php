<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestPost extends Model
{
    protected $fillable = [
        'title',
        'description',
    ];
}
