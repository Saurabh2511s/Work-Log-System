<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
        {
            Schema::create('time_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->date('work_date');
                $table->foreignId('project_id')->constrained()->restrictOnDelete();
                $table->string('description', 500);
                $table->unsignedInteger('minutes'); // stored as minutes
                $table->timestamps();

                $table->index(['user_id','work_date']);
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_logs');
    }
};
