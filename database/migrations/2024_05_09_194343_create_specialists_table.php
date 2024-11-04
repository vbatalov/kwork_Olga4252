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
        Schema::create('specialists', function (Blueprint $table) {
            $table->id();
            $table->string("peer_id")->unique();
            $table->string("name")->nullable();
            $table->string("surname")->nullable();
            $table->string("role")->default("specialist");
            $table->integer("percent")->default("80");
            $table->double("balance")->default("0");
            $table->string("cookie")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specialists');
    }
};
