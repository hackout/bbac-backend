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
        Schema::create('vehicle_targets', function (Blueprint $table) {
            $table->id();
            $table->year('yearly')->comment("年份");
            $table->integer('eb_type')->default(0)->comment("发动机类型");
            $table->integer('target')->default(0)->comment("目标值");
            $table->timestamps();
            $table->comment = "PPM目标值";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_targets');
    }
};
