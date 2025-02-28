<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TestUser extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
    ];

    public function testPosts(): HasMany
    {
        return $this->hasMany(TestPost::class);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(TestComment::class, 'commentable');
    }
}
