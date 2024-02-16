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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer("user_id");
            $table->integer("category_id");
            $table->integer("subject_id");
            $table->string("description")->nullable();
            $table->dateTime("deadline");
            $table->string("status")->default("open");
            $table->integer("executor_id");
            $table->string("completion_date");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
