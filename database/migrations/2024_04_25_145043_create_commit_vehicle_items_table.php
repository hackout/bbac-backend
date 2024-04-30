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
        Schema::create('commit_vehicle_items', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique()->comment("主键");
            $table->uuid('unique_id')->nullable()->comment("唯一标识");
            $table->uuid('commit_vehicle_id')->nullable()->comment('整车服务考核(历史)ID');
            $table->longText("content")->nullable()->comment("工作内容");
            $table->longText("content_en")->nullable()->comment("工作内容(英)");
            $table->longText("standard")->nullable()->comment("检查标准");
            $table->longText("standard_en")->nullable()->comment("检查标准(英)");
            $table->longText("other")->nullable()->comment("其他要求");
            $table->longText("other_en")->nullable()->comment("其他要求(英)");
            $table->tinyInteger('type')->default(0)->comment("考核项类型");
            $table->integer('sort_order')->default(0)->comment('序号');
            $table->timestamps();
            $table->comment = "整车服务-考核项列表";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commit_vehicle_items');
    }
};
