<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestPost extends Model
{
    protected $fillable = [
        'title',
        'description',
    ];

    public function testUser(): BelongsTo
    {
        return $this->belongsTo(TestUser::class);
    }
}
