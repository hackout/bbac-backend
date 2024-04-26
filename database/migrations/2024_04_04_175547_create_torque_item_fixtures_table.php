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
        Schema::create('torque_item_fixtures', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique()->comment("主键");
            $table->uuid('torque_id')->nullable()->comment("扭矩数据ID");
            $table->uuid('torque_item_id')->nullable()->comment("螺栓ID");
            $table->integer('stage')->default(0)->comment('阶段');
            $table->decimal("value")->default(0)->comment('扭矩');
            $table->decimal("angle")->default(0)->comment('角度');
            $table->decimal("speed")->default(0)->comment('转速');
            $table->timestamps();
            $table->comment = "紧固参数表";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('torque_item_fixtures');
    }
};
