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
        Schema::create('examine_product_items', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique()->comment("主键");
            $table->uuid('examine_product_id')->nullable()->comment('产品考核-模板ID');
            $table->uuid('commit_product_item_id')->nullable()->comment('考核项历史ID');
            $table->uuid('unique_id')->nullable()->comment("唯一标识");
            $table->uuid('part_id')->nullable()->comment('零件ID');
            $table->longText("name")->nullable()->comment("测量项");
            $table->longText("name_en")->nullable()->comment("测量项");
            $table->longText("content")->nullable()->comment("内容");
            $table->longText("content_en")->nullable()->comment("内容");
            $table->longText("standard")->nullable()->comment("操作描述");
            $table->longText("standard_en")->nullable()->comment("操作描述");
            $table->longText("eye")->nullable()->comment("目视检查");
            $table->longText("eye_en")->nullable()->comment("目视检查");
            $table->integer("number")->default(0)->comment('数量');
            $table->decimal("lower_limit")->default(0)->comment('测量下限');
            $table->decimal("upper_limit")->default(0)->comment('测量上限');
            $table->string("unit")->nullable()->comment('测量单位');
            $table->string("torque")->nullable()->comment('拧紧扭矩要求');
            $table->boolean('is_scan')->default(false)->comment('是否扫码');
            $table->boolean('is_camera')->default(false)->comment('是否拍照');
            $table->longText('scan')->nullable()->comment('扫码说明');
            $table->longText('camera')->nullable()->comment('拍照说明');
            $table->longText('record')->nullable()->comment('记录提示');
            $table->decimal("process")->default(0)->comment('进度');
            $table->tinyInteger('type')->default(0)->comment("考核项类型");
            $table->integer('sort_order')->default(0)->comment('序号');
            $table->json('options')->nullable()->comment('附加选项');
            $table->timestamps();
            $table->comment = "产品考核-考核项列表";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examine_product_items');
    }
};
