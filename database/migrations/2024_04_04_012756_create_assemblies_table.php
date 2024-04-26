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
        Schema::create('assemblies', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique()->comment("主键");
            $table->integer('type')->default(0)->comment('发送机型号');
            $table->integer('plant')->default(0)->comment("工厂");
            $table->integer('line')->default(0)->comment("生产线");
            $table->integer('status')->default(0)->comment("项目阶段");
            $table->string('number')->unique()->comment("总成号");
            $table->string('remark')->nullable()->comment("备注信息");
            $table->timestamps();
            $table->comment = "基础总成信息表";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assemblies');
    }
};
