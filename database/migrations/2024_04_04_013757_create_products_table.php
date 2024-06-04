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
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique()->comment("主键");
            $table->integer('line')->default(0)->comment("产线");
            $table->integer('plant')->default(0)->comment("工厂");
            $table->integer('engine')->default(0)->comment("机型");
            $table->integer('status')->default(0)->comment("阶段状态");
            $table->uuid('assembly_id')->nullable()->comment('总成号');
            $table->string('number')->unique()->comment("发动机号");
            $table->timestamp('beginning_at')->nullable()->comment('接机时间');
            $table->timestamp('examine_at')->nullable()->comment('考核时间');
            $table->timestamp('qc_at')->nullable()->comment('热试时间');
            $table->timestamp('assembled_at')->nullable()->comment('装配时间');
            $table->timestamps();
            $table->comment = "发动机产品表";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
