<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('torque_item_monitors', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique()->comment("主键");
            $table->uuid('torque_id')->nullable()->comment("扭矩数据ID");
            $table->uuid('torque_item_id')->nullable()->comment("螺栓ID");
            $table->boolean('is_ipm')->default(false)->comment('IPM连接');
            $table->boolean('is_into')->default(false)->comment('进入状态');
            $table->integer('stage')->default(0)->comment('阶段');
            $table->integer('status')->default(0)->comment('扭矩过程监控状态');
            $table->decimal("threshold")->default(0)->comment('扭矩阈值');
            $table->decimal("lower")->default(0)->comment('扭矩公差下限');
            $table->decimal("upper")->default(0)->comment('扭矩公差上限');
            $table->decimal("angle_lower")->default(0)->comment('角度公差下限');
            $table->decimal("angle_upper")->default(0)->comment('角度公差上限');
            $table->decimal("gradient_lower")->default(0)->comment('坡度公差下限');
            $table->decimal("gradient_upper")->default(0)->comment('坡度公差上限');
            $table->timestamps();
            $table->comment = "扭矩过程监控表";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('torque_item_monitors');
    }
};
