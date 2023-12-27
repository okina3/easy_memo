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
        Schema::create('share_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('sharing_user_id');
            $table->foreignId('memo_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->boolean('edit_access');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('share_settings');
    }
};
