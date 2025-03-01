<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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

    public function testComments(): MorphMany
    {
        return $this->morphMany(TestComment::class, 'commentable');
    }
}
