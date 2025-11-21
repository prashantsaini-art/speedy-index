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
Schema::create('api_logs', function (Blueprint $table) {
$table->id();
$table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
$table->string('endpoint');
$table->string('method');
$table->json('request_data')->nullable();
$table->json('response_data')->nullable();
$table->integer('status_code')->nullable();
$table->text('error_message')->nullable();
$table->decimal('response_time', 8, 3)->nullable();
$table->timestamps();
$table->index('created_at');
});
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_logs');
    }
};
