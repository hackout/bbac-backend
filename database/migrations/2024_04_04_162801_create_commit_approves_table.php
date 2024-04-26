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
        Schema::create('commit_approves', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique()->comment("主键");
            $table->nullableUuidMorphs('commit');
            $table->uuid('user_id')->index()->comment('申请人');
            $table->uuid('messenger_id')->index()->comment('传达人');
            $table->uuid('approver_id')->index()->comment('审核人');
            $table->string("content")->nullable()->comment("变更内容");
            $table->string("influence")->nullable()->comment("影响范围");
            $table->string("concerns")->nullable()->comment("关注事项");
            $table->json("extra")->nullable()->comment("影响区域参数");
            $table->string("description")->nullable()->comment("审批说明");
            $table->boolean("status")->default(0)->comment('审核状态');
            $table->timestamps();
            $table->comment = "考核单审核记录";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commit_approves');
    }
};
