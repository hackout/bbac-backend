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
        Schema::create('documents', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique()->comment("主键");
            $table->uuid('user_id')->nullable()->comment('用户ID');
            $table->string("name")->comment('指导书名称');
            $table->integer('engine')->default(0)->comment("发动机型号");
            $table->boolean('is_valid')->default(false)->comment('启用状态');
            $table->tinyInteger('type')->default(0)->comment("知道书类型");
            $table->timestamps();
            $table->comment = "指导书";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
