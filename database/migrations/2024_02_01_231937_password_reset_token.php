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
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->id();
            $table->uuid("user_id")->constrained('users', 'id')->comment("用户ID");
            $table->string("token")->unique()->index()->comment("重置Token");
            $table->string("account")->comment("账号");
            $table->enum("type", ['account', 'email', 'mobile', 'number'])->default('account')->comment('账号类型');
            $table->timestamp('created_at')->comment("创建时间")->nullable();
            $table->timestamp('valid_at')->comment("失效时间")->nullable();
            $table->comment = "找回密码校验表";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
    }
};
