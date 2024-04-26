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
        Schema::create('torque_item_details', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique()->comment("主键");
            $table->uuid('torque_id')->nullable()->comment("扭矩数据ID");
            $table->uuid('torque_item_id')->nullable()->comment("螺栓ID");
            $table->string('remark')->nullable()->comment('备注说明');
            $table->decimal("cp")->default(0)->comment('CP');
            $table->decimal("cpk")->default(0)->comment('CPK');
            $table->date('last_date')->nullable()->comment("月份");
            $table->timestamps();
            $table->comment = "扭矩SPC数据表";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('torque_item_details');
    }
};
