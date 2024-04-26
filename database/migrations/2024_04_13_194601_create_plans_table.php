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
        Schema::create('plans', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique()->comment("主键");
            $table->uuid('assembly_id')->nullable()->comment('总成ID');
            $table->integer('plant')->default(0)->comment('工厂');
            $table->integer('line')->default(0)->comment('产线');
            $table->integer('type')->default(0)->comment('机型');
            $table->integer('quantity')->default(0)->comment('计划产量');
            $table->integer('actual_quantity')->default(0)->comment('已排产数量');
            $table->timestamp('plan_at')->nullable()->comment("计划生产时间");
            $table->boolean('is_full')->default(false)->comment('是否已排产完');
            $table->string("remark")->nullable()->comment("备注");
            $table->timestamps();
            $table->comment = "排产计划表";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
