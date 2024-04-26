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
        Schema::create('training_users', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique()->comment("主键");
            $table->uuid("user_id")->nullable()->comment("员工ID");
            $table->uuid("training_id")->nullable()->comment("培训ID");
            $table->string("name")->comment("参与人姓名");
            $table->string("number")->comment("参与人员工号");
            $table->integer("status")->default(0)->comment("参与状态");
            $table->timestamp("trained_at")->nullable()->comment("培训时间");
            $table->timestamps();
            $table->comment = "培训员工表";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_users');
    }
};
