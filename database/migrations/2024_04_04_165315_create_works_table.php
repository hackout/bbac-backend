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
        Schema::create('works', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id')->nullable()->comment("员工ID");
            $table->decimal('period')->default(0)->comment("工时");
            $table->decimal('available_period')->default(0)->comment("可用工时");
            $table->date("day")->nullable()->comment('日期');
            $table->timestamps();
            $table->comment = "员工每日考勤工时统计";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('works');
    }
};
