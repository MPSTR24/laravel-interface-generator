<?php

namespace Mpstr24\InterfaceTyper\Tests\fake\database\migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('test_comments', function (Blueprint $table) {
            $table->id();
            $table->text('body');
            $table->morphs('commentable');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_comments');
    }
};
