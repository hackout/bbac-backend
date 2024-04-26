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
        Schema::create('departments', function (Blueprint $table) {
            $table->uuid('id')->primary()->index()->comment("部门主键");
            $table->uuid('parent_id')->nullable()->comment("上级部门");
            $table->uuid('leader_id')->nullable()->comment("部门负责人用户ID");
            $table->string('name')->comment("部门名称");
            $table->string('contact')->nullable()->comment('负责人');
            $table->string('mobile')->nullable()->comment('联系手机');
            $table->string('email')->nullable()->comment('联系邮箱');
            $table->integer("role")->default(0)->comment('部门属性');
            $table->comment = "部门信息表";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
