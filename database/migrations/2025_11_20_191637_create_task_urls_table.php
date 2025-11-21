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
        // Ensure 'tasks' table exists before creating this
        if (!Schema::hasTable('tasks')) {
            throw new \Exception('The "tasks" table does not exist. Please run the tasks migration first.');
        }

        Schema::create('task_urls', function (Blueprint $table) {
            $table->id();
            
            // This assumes 'tasks' table exists and has an 'id' column of type unsignedBigInteger
            $table->foreignId('task_id')
                  ->constrained('tasks')
                  ->onDelete('cascade');
            
            $table->text('url');
            $table->string('status')->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('indexed_at')->nullable();
            $table->timestamps();

            // Index for faster queries
            $table->index(['task_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_urls');
    }
};
