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
        Schema::create('issue_inline_logs', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique()->comment("主键");
            $table->uuid('issue_inline_id')->nullable()->comment("在线考核-问题ID");
            $table->uuid('user_id')->nullable()->comment("用户ID");
            $table->string("code")->comment("字段标识");
            $table->longText("content")->comment("操作内容");
            $table->timestamps();
            $table->comment = "在线考核问题操作记录";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_inline_logs');
    }
};
