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
        Schema::create('torque_items', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique()->comment("主键");
            $table->uuid('torque_id')->nullable()->comment("扭矩数据ID");
            $table->uuid('product_id')->nullable()->comment("发动机信息");
            $table->string('number')->nullable()->comment('螺栓编号(子编号)');
            $table->boolean('is_issue')->default(false)->comment('是否问题件');
            $table->timestamps();
            $table->comment = "扭矩螺栓数据表";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('torque_items');
    }
};
