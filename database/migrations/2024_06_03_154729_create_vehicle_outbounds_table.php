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
        Schema::create('vehicle_outbounds', function (Blueprint $table) {
            $table->id();
            $table->date('daily')->comment("日期");
            $table->integer('outbound')->default(0)->comment("发运数量");
            $table->integer('eb_type')->default(0)->comment("发动机类型");
            $table->timestamps();
            $table->comment = "整车服务每日发运数量";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_outbounds');
    }
};
