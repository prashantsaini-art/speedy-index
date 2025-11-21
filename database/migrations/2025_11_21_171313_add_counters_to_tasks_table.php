<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->integer('total_urls')->default(0);
            $table->integer('indexed_count')->default(0);
            $table->integer('pending_count')->default(0);
            $table->integer('error_count')->default(0);
            $table->json('metadata')->nullable(); // Also good to have if missing
            $table->string('external_task_id')->nullable(); // Ensure this exists too
        });
    }

    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn([
                'total_urls',
                'indexed_count',
                'pending_count',
                'error_count',
                'metadata',
                'external_task_id',
            ]);
        });
    }
};
