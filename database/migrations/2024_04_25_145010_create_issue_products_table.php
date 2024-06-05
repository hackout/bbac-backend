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
        Schema::create('issue_products', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique()->comment("主键");
            $table->uuid('author_id')->nullable()->comment("提交用户ID");
            $table->uuid('user_id')->nullable()->comment("处理用户ID");
            $table->uuid('task_id')->nullable()->comment('考核单ID');
            $table->uuid('task_item_id')->nullable()->comment('考核单ID');
            $table->integer('plant')->default(0)->comment('工厂');
            $table->integer('line')->default(0)->comment('产线');
            $table->integer('engine')->default(0)->comment('发动机型号');
            $table->integer("stage")->default(0)->comment("项目阶段");
            $table->uuid('assembly_id')->nullable()->comment("总成号ID");
            $table->uuid('product_id')->nullable()->comment("发动机ID");
            $table->uuid('part_id')->nullable()->comment("零件ID");
            $table->integer("defect_description")->default(0)->comment("缺陷描述");
            $table->integer("defect_level")->default(0)->comment("问题等级");
            $table->integer("defect_part")->default(0)->comment("缺陷零件");
            $table->integer("defect_position")->default(0)->comment("问题位置");
            $table->integer("defect_cause")->default(0)->comment("具体位置");
            $table->longText("cause")->nullable()->comment("原因说明");
            $table->longText("soma")->nullable()->comment("短期措施");
            $table->longText("lama")->nullable()->comment("长期措施");
            $table->longText("note")->nullable()->comment("备注信息");
            $table->longText("eight_disciplines")->nullable()->comment("8D");
            $table->string("ira")->nullable()->comment("责任人");
            $table->integer("score_card")->default(0)->comment("Score Card");
            $table->string("department")->nullable()->comment("责任部门");
            $table->integer("status")->default(0)->comment("问题状态");
            $table->integer("type")->default(0)->comment("考核类型");
            $table->boolean("is_ok")->default(false)->comment("是否OK");
            $table->timestamps();
            $table->comment = "产品考核-问题信息表";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_products');
    }
};
