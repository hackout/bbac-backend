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
        Schema::create('task_items', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique()->comment("主键");
            $table->uuid('user_id')->nullable()->comment("员工ID");
            $table->uuid('task_id')->nullable()->comment("考核单ID");
            $table->uuid('examine_item_id')->nullable()->comment("考核项ID");
            $table->integer('sort_order')->default(0)->comment("排序");
            $table->longText('content')->nullable()->comment('用户填写内容');
            $table->json("extra")->nullable()->comment("原始信息");
            $table->string("remark")->nullable()->comment('标注信息');
            $table->timestamps();
            $table->comment = "考核操作详情";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_items');
    }
};
