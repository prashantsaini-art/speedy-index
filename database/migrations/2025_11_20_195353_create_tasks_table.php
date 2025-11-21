<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id(); // This creates an unsignedBigInteger 'id'
            $table->string('title')->nullable();
            $table->string('type'); // 'indexer' or 'checker'
            $table->string('search_engine'); // 'google' or 'yandex'
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};
