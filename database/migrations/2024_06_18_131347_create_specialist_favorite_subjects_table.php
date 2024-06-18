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
        Schema::create('specialist_favorite_subjects', function (Blueprint $table) {
            $table->id();
            $table->integer("user_id");
            $table->foreign("user_id")->references("id")->on("specialists");

            $table->integer("favorite_subject_id");
            $table->foreign("favorite_subject_id")->references("id")->on("subjects");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specialist_favorite_subjects');
    }
};
