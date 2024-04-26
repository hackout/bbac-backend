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
        Schema::create('trainings', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique()->comment("主键");
            $table->string("name")->comment("培训名称");
            $table->integer('type')->default(0)->comment("培训类型");
            $table->integer("status")->default(0)->comment("培训状态");
            $table->timestamp("started_at")->nullable()->comment("培训时间");
            $table->integer("period")->default(0)->comment("培训课时");
            $table->timestamp("ended_at")->nullable()->comment("培训完成时间");
            $table->timestamps();
            $table->comment = "培训信息表";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainings');
    }
};
