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
        Schema::create('work_items', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique()->comment("主键");
            $table->uuid('user_id')->nullable()->comment("员工ID");
            $table->uuid('task_id')->nullable()->comment("考核单ID");
            $table->integer('work_id')->default(0)->comment("WorkId");
            $table->string("content")->nullable()->comment("说明");
            $table->integer('type')->default(0)->comment('类型');
            $table->integer('status')->default(0)->comment('状态');
            $table->string('period')->default(0)->comment("工时");
            $table->json('extra')->nullable()->comment("考核单参数");
            $table->timestamp('work_date')->nullable()->comment('工作日');
            $table->timestamps();
            $table->comment = "考勤记录";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_items');
    }
};
