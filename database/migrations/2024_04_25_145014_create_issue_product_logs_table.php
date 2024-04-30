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
        Schema::create('issue_product_logs', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique()->comment("主键");
            $table->uuid('issue_product_id')->nullable()->comment("产品考核-问题ID");
            $table->uuid('user_id')->nullable()->comment("用户ID");
            $table->string("code")->comment("字段标识");
            $table->json('extra')->nullable()->comment("变更内容");
            $table->timestamps();
            $table->comment = "产品考核问题操作记录";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_product_logs');
    }
};
