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
        Schema::create('system_configs', function (Blueprint $table) {
            $table->string('code')->primary()->comment("主键");
            $table->longText('content')->nullable()->comment("内容值");
            $table->string('label')->comment("中文键名");
            $table->smallInteger('type')->default(0)->comment('参数类型');
            $table->comment = "系统设置参数";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_configs');
    }
};
