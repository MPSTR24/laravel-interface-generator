<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TestComment extends Model
{
    protected $fillable = [
        'body',
    ];

    public function testCommentable(): MorphTo
    {
        return $this->morphTo();
    }
}
