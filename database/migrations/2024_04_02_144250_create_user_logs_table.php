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
        Schema::create('user_logs', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique()->comment("主键");
            $table->uuid('user_id')->constrained('users', 'id')->comment("用户主键");
            $table->string('name')->comment("操作说明");
            $table->string('route')->comment("访问地址");
            $table->boolean('status')->default(true)->comment("请求状态");
            $table->ipAddress('ip_address')->nullable()->comment("请求IP");
            $table->json('extra')->nullable()->comment("附加操作");
            $table->enum('method', ['GET', 'POST', 'DELETE', 'PUT', 'PATCH'])->default('GET')->comment("请求类型");
            $table->string('os')->default('backend')->comment('端口类型');
            $table->timestamp('created_at')->comment("发生时间");
            $table->comment = "操作记录表";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_logs');
    }
};
