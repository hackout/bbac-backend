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
        Schema::create('birthday_cards', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment("主键");
            $table->string("name")->comment("卡片名称");
            $table->json("design")->nullable()->comment("设计");
            $table->timestamps();
            $table->comment = "生日卡样式模板";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('birthday_cards');
    }
};
