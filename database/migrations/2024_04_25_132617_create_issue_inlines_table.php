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
        Schema::create('issue_inlines', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique()->comment("主键");
            $table->uuid('author_id')->nullable()->comment("提交用户ID");
            $table->uuid('user_id')->nullable()->comment("处理用户ID");
            $table->uuid('task_id')->nullable()->comment('考核单ID');
            $table->integer('plant')->default(0)->comment('工厂');
            $table->integer('line')->default(0)->comment('产线');
            $table->integer('engine')->default(0)->comment('发动机型号');
            $table->integer("stage")->default(0)->comment("项目阶段");
            $table->string('station')->nullable()->comment("工位");
            $table->uuid('assembly_id')->nullable()->comment("总成号ID");
            $table->uuid('product_id')->nullable()->comment("发动机ID");
            $table->longText("affect_scope")->nullable()->comment("影响范围");
            $table->string("ira")->nullable()->comment("责任人");
            $table->integer("issue_description")->default(0)->comment("问题描述");
            $table->integer("defect_level")->default(0)->comment("缺陷等级");
            $table->longText("reason")->nullable()->comment("原因分级");
            $table->longText("cause")->nullable()->comment("根本原因");
            $table->integer("category")->default(0)->comment("问题等级");
            $table->longText("soma")->nullable()->comment("短期措施");
            $table->date("soma_due")->nullable()->comment("短期措施-节点");
            $table->longText("lama")->nullable()->comment("长期措施");
            $table->date("lama_due")->nullable()->comment("长期措施-节点");
            $table->longText("note")->nullable()->comment("备注信息");
            $table->longText("eight_disciplines")->nullable()->comment("8D");
            $table->integer("status")->default(0)->comment("问题状态");
            $table->integer("type")->default(0)->comment("考核类型");
            $table->timestamps();
            $table->comment = "在线考核-问题信息表";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_inlines');
    }
};
