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
        Schema::create('task_crons', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique()->comment("主键");
            $table->uuid('user_id')->nullable()->comment("用户ID");
            $table->uuid('examine_id')->nullable()->comment("考核单ID");
            $table->integer('type')->default(0)->comment('考核类型');
            $table->string("name")->comment('考核名称');
            $table->integer('plant')->default(0)->comment('工厂');
            $table->integer('line')->default(0)->comment('产线');
            $table->integer('engine')->default(0)->comment('发动机型号');
            $table->integer('status')->default(0)->comment("阶段状态");
            $table->uuid('assembly_id')->nullable()->comment("总成号ID");
            $table->json('days')->nullable()->comment("天数");
            $table->integer('yield')->default(0)->comment("排产");
            $table->integer('yield_unit')->default(0)->comment('排产次');
            $table->boolean('is_valid')->default(true)->comment('是否启用');
            $table->decimal("period")->default(0)->comment("工时");
            $table->timestamps();
            $table->comment = "定时任务";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_crons');
    }
};
