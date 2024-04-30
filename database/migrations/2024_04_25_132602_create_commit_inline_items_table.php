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
        Schema::create('commit_inline_items', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique()->comment("主键");
            $table->uuid('commit_inline_id')->nullable()->comment('在线考核(历史)ID');
            $table->uuid('unique_id')->nullable()->comment("唯一标识");
            $table->string("station")->nullable()->comment('工位');
            $table->string("name")->nullable()->comment("测量项");
            $table->longText("content")->nullable()->comment("中文内容");
            $table->longText("content_en")->nullable()->comment("英文内容");
            $table->longText("standard")->nullable()->comment("检查标准");
            $table->longText("standard_en")->nullable()->comment("检查标准(英)");
            $table->integer("number")->default(0)->comment('数量');
            $table->tinyInteger("special")->default(0)->comment('特殊特性');
            $table->string('gluing')->nullable()->comment('墨水型号');
            $table->string('bolt_number')->nullable()->comment('螺栓编号');
            $table->tinyInteger('bolt_model')->default(0)->comment('螺栓分类1');
            $table->tinyInteger('bolt_type')->default(0)->comment('螺栓分类2');
            $table->tinyInteger('bolt_status')->default(0)->comment('放行状态');
            $table->decimal("lower_limit")->default(0)->comment('测量下限');
            $table->decimal("upper_limit")->default(0)->comment('测量上限');
            $table->string("unit")->nullable()->comment('测量单位');
            $table->tinyInteger('type')->default(0)->comment("考核项类型");
            $table->integer('sort_order')->default(0)->comment('序号');
            $table->json('options')->nullable()->comment('附加选项');
            $table->timestamps();
            $table->comment = "在线考核-考核项列表";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commit_inline_items');
    }
};
