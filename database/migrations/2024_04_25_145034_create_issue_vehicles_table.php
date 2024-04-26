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
        Schema::create('issue_vehicles', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique()->comment("主键");
            $table->uuid('author_id')->nullable()->comment("提交用户ID");
            $table->uuid('user_id')->nullable()->comment("处理用户ID");
            $table->uuid('confirm_id')->nullable()->comment("确认工程师ID");
            $table->uuid('task_id')->nullable()->comment('考核单ID');
            $table->tinyInteger('shift')->default(0)->comment("班次");
            $table->integer('eb_type')->default(0)->comment("发动机/电池型号");
            $table->string('product_number')->nullable()->comment("生产单号");
            $table->tinyInteger('sensor')->default(0)->comment("问题发现点");
            $table->string('eb_number')->nullable()->comment("发动机/电池号");
            $table->tinyInteger('car_line')->default(0)->comment("车系");
            $table->tinyInteger('car_type')->default(0)->comment("车型");
            $table->boolean("is_block")->default(false)->comment("是否OK");
            $table->longText("description")->nullable()->comment("问题描述");
            $table->longText("initial_analysis")->nullable()->comment("现场分析");
            $table->longText("initial_action")->nullable()->comment("分析行动");
            $table->integer("status")->default(0)->comment("问题状态");
            $table->integer("type")->default(0)->comment("问题类型");
            $table->integer("defect_level")->default(0)->comment("问题等级");
            $table->longText("soma")->nullable()->comment("短期措施");
            $table->longText("lama")->nullable()->comment("长期措施");
            $table->longText("eight_disciplines")->nullable()->comment("8D");
            $table->string("ira")->nullable()->comment("责任人");
            $table->boolean("is_confirm")->default(false)->comment("是否确认");
            $table->boolean("is_ppm")->default(false)->comment("是否PPM");
            $table->boolean("is_pre_highlight")->default(false)->comment("是否PPM");
            $table->integer("detect_area")->default(0)->comment("探测区域");
            $table->integer("quantity")->default(0)->comment("问题数量");
            $table->longText("cause")->nullable()->comment("根本原因");
            $table->longText("relate_parts")->nullable()->comment("关联零件");
            $table->integer("cause_type")->default(0)->comment("根本原因类型");
            $table->timestamp('due_date')->nullable()->comment('预计处理时间');
            $table->timestamps();
            $table->comment = "整车服务-问题信息表";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_vehicles');
    }
};
