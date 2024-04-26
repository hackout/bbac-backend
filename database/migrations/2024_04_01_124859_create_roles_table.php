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
        Schema::create('roles', function (Blueprint $table) {
            $table->uuid('id')->primary()->index()->comment("主键");
            $table->string('name')->comment("名称");
            $table->json('permissions')->nullable()->comment("权限");
            $table->boolean('is_valid')->default(true)->comment("是否启用");
            $table->comment = "用户角色表";
        });
        Schema::create('users_roles', function (Blueprint $table) {
            $table->uuid('user_id');
            $table->uuid('role_id');
            $table->primary(['user_id', 'role_id'], 'user_role');
            $table->comment = "用户角色关联表";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
        Schema::dropIfExists('users_roles');
    }
};
