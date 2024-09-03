<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->nullable()->constrained("users")->nullOnDelete();
            $table->foreignId("category_id")->nullable()->constrained("categories")->nullOnDelete();
            $table->foreignId("subject_id")->nullable()->constrained("subjects")->nullOnDelete();
            $table->longText("need_help_with")->nullable();
            $table->longText("description")->nullable();
            $table->string("deadline")->nullable();
            $table->enum("status", [
                "draft", "pending", "wait_payment", "progress", "finish"
            ])->default("draft");
            $table->integer("executor_id")->nullable();
            $table->string("completion_date")->nullable();
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
