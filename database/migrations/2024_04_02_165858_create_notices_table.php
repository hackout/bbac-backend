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
        Schema::create('notices', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment("主键");
            $table->uuid("user_id")->nullable()->index()->comment("发布者");
            $table->string("title")->comment("消息标题");
            $table->string("type")->default('message')->index()->comment("消息类型");
            $table->string('content')->nullable()->comment("消息内容");
            $table->string('from')->nullable()->comment("来源");
            $table->boolean("is_sent")->default(false)->comment("是否发送");
            $table->boolean('is_valid')->default(true)->comment("是否生效");
            $table->json("extra")->nullable()->comment("附加信息");
            $table->nullableUuidMorphs('model');
            $table->timestamps();
            $table->comment = "消息信息表";
        });

        Schema::create('users_notices', function (Blueprint $table) {
            $table->uuid('user_id')->comment("用户ID");
            $table->uuid('notice_id')->comment("消息ID");
            $table->boolean('is_read')->default(false)->comment("是否已读");
            $table->comment = "用户消息关联表";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notices');
        Schema::dropIfExists('users_notices');
    }
};
