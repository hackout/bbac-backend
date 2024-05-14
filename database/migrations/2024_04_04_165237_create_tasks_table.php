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
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique()->comment("主键");
            $table->string("name")->comment('考核名称');
            $table->uuid('examine_id')->nullable()->comment("模板ID");
            $table->uuid('task_cron_id')->nullable()->comment("任务模板ID");
            $table->uuid('user_id')->nullable()->comment("员工ID");
            $table->integer('type')->default(0)->comment('考核类型');
            $table->integer('plant')->default(0)->comment('工厂');
            $table->integer('line')->default(0)->comment('产线');
            $table->integer('engine')->default(0)->comment('发动机型号');
            $table->integer('status')->default(0)->comment("阶段状态");
            $table->json('extra')->nullable()->comment("扩展信息");
            $table->longText('remark')->nullable()->comment("备注信息");
            $table->string('eb_number')->nullable()->comment('发动机号');
            $table->uuid('assembly_id')->nullable()->comment("总成ID");
            $table->string('task_status')->nullable()->comment('任务状态');
            $table->string('number')->unique()->comment('考核单号');
            $table->decimal("period")->default(0)->comment("工时");
            $table->date("start_at")->nullable()->comment("开始考核时间");
            $table->date("end_at")->nullable()->comment("结束考核时间");
            $table->timestamp("valid_at")->nullable()->comment("过期时间");
            $table->json("original_examine")->nullable()->comment("原始考核模板信息");
            $table->timestamps();
            $table->comment = "考核单";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
