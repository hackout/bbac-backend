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
        Schema::create('examine_vehicle_items', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique()->comment("主键");
            $table->uuid('examine_vehicle_id')->nullable()->comment('动态考核-模板ID');
            $table->uuid('commit_vehicle_item_id')->nullable()->comment('考核项历史ID');
            $table->uuid('unique_id')->nullable()->comment("唯一标识");
            $table->string("content")->nullable()->comment("工作内容");
            $table->string("content_en")->nullable()->comment("工作内容(英)");
            $table->string("standard")->nullable()->comment("检查标准");
            $table->string("standard_en")->nullable()->comment("检查标准(英)");
            $table->string("other")->nullable()->comment("其他要求");
            $table->string("other_en")->nullable()->comment("其他要求(英)");
            $table->tinyInteger('type')->default(0)->comment("考核项类型");
            $table->integer('sort_order')->default(0)->comment('序号');
            $table->timestamps();
            $table->comment = "整车服务动态考核-考核项列表";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examine_vehicle_items');
    }
};
