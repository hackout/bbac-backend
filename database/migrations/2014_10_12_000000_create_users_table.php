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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique()->comment("主键");
            $table->uuid('department_id')->index()->nullable()->comment("部门ID");
            $table->timestamp('lasted_login')->nullable()->comment("最后登录");
            $table->ipAddress('lasted_ip_address')->nullable()->comment("最后登录IP");
            $table->string('password');
            $table->boolean('is_valid')->default(true)->comment("是否可用");
            $table->boolean('is_super')->default(false)->comment("是否超管");
            $table->integer('has_error')->default(false)->comment("登录是否报错");
            $table->integer('is_lock')->default(false)->comment("是否锁定");
            $table->integer('failed_count')->default(0)->comment("失败次数");
            $table->rememberToken();
            $table->timestamps();
            $table->comment = "用户信息表";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
