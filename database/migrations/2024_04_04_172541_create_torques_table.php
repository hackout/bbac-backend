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
        Schema::create('torques', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique()->comment("主键");
            $table->uuid('author_id')->nullable()->comment('发布者ID');
            $table->uuid('user_id')->nullable()->comment('用户ID');
            $table->integer('plant')->default(0)->comment('工厂');
            $table->integer('line')->default(0)->comment('产线');
            $table->integer('engine')->default(0)->comment('发动机型号');
            $table->integer('vehicle_type')->default(0)->comment("车型");
            $table->uuid('assembly_id')->nullable()->comment("总成号ID");
            $table->string('number')->nullable()->comment('螺栓编号');
            $table->string("content_zh")->nullable()->comment("描述-中文");
            $table->string("content_en")->nullable()->comment("描述-英文");
            $table->integer('quantity')->default(0)->comment('螺栓数量');
            $table->integer('model')->default(0)->comment('螺栓分类1');
            $table->integer('type')->default(0)->comment('螺栓分类2');
            $table->integer('status')->default(0)->comment('放行状态');
            $table->integer('stage')->default(0)->comment('项目阶段');
            $table->string("station")->nullable()->comment('工位');
            $table->string("sub_station")->nullable()->comment('工位2');
            $table->integer("special")->default(0)->comment('特殊特性');
            $table->string('param')->nullable()->comment('螺栓参数');
            $table->decimal("torque_target")->default(0)->comment('目标扭矩');
            $table->decimal("torque_lower")->default(0)->comment('扭矩下限');
            $table->decimal("torque_upper")->default(0)->comment('扭矩上限');
            $table->decimal("angle_target")->default(0)->comment('角度标准');
            $table->decimal("angle_lower")->default(0)->comment('角度下限');
            $table->decimal("angle_upper")->default(0)->comment('角度上限');
            $table->date("lasted_at")->nullable()->comment("最近放行时间");
            $table->date("expected_at")->nullable()->comment("预计放行时间");
            $table->date("final_at")->nullable()->comment("最终放行时间");
            $table->decimal("start_torque")->default(0)->comment('起始扭矩');
            $table->decimal("residual_torque")->default(0)->comment('转矩角');
            $table->decimal("pfu_test")->default(0)->comment('PFU-测试值');
            $table->decimal("pfu_lower")->default(0)->comment('PFU-考核下限');
            $table->decimal("pfu_upper")->default(0)->comment('PFU-考核上限');
            $table->decimal("pfu_early_lower")->default(0)->comment('PFU-预警上限');
            $table->decimal("pfu_early_upper")->default(0)->comment('PFU-预警下限');
            $table->decimal("l_pfu_test")->default(0)->comment('L-PFU-测试值');
            $table->decimal("l_pfu_lower")->default(0)->comment('L-PFU-考核下限');
            $table->decimal("l_pfu_upper")->default(0)->comment('L-PFU-考核上限');
            $table->decimal("l_pfu_early_lower")->default(0)->comment('L-PFU-预警上限');
            $table->decimal("l_pfu_early_upper")->default(0)->comment('L-PFU-预警下限');
            $table->timestamps();
            $table->comment = "扭矩数据库";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('torques');
    }
};
