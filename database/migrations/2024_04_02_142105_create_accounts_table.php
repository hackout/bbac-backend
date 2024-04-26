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
        Schema::create('accounts', function (Blueprint $table) {
            $table->string('account')->primary()->comment("账号");
            $table->uuid("user_id")->constrained('users','id')->comment("用户ID");
            $table->enum("type", ['account', 'email', 'mobile', 'number'])->default('account')->comment('账号类型');
            $table->json("extra")->nullable()->comment('附加参数');
            $table->timestamp("verified_at")->nullable()->comment("验证时间");
            $table->timestamps();
            $table->comment = "账号信息";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
