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
        Schema::create('torque_change_records', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique()->comment("主键");
            $table->uuid('approver_id')->nullable()->comment('审核人ID');
            $table->uuid('user_id')->nullable()->comment('用户ID');
            $table->uuid('torque_id')->nullable()->comment('扭矩ID');
            $table->json("extra")->comment('变更项');
            $table->boolean('is_io')->default(false)->comment('是否IO变更');
            $table->integer('status')->default(0)->comment("审批状态");
            $table->timestamps();
            $table->comment = "扭矩变更记录";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('torque_change_records');
    }
};
