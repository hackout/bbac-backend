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
        Schema::create('commit_products', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique()->comment("主键");
            $table->uuid('author_id')->nullable()->comment('发布者ID');
            $table->uuid('user_id')->nullable()->comment('用户ID');
            $table->uuid('examine_product_id')->nullable()->comment('应用考核ID');
            $table->uuid('parent_id')->nullable()->comment('上一个版本');
            $table->string("version")->nullable()->comment("版本号");
            $table->string("name")->comment('模板名称');
            $table->string("description")->nullable()->comment('说明备注');
            $table->integer('engine')->default(0)->comment("发动机型号");
            $table->decimal('period')->default(0)->comment("标准工时");
            $table->boolean('is_valid')->default(false)->comment('启用状态');
            $table->tinyInteger('status')->default(0)->comment("考核单状态");
            $table->tinyInteger('type')->default(0)->comment("考核单类型");
            $table->timestamps();
            $table->comment = "产品考核(历史)";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commit_products');
    }
};
