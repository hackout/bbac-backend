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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id')->constrained('users', 'id')->comment("用户ID");
            $table->uuid('birthday_card_id')->nullable()->comment("生日卡ID");
            $table->string("name")->nullable()->comment("姓名");
            $table->string("pinyin")->nullable()->comment("姓名拼音");
            $table->smallInteger("gender")->nullable()->comment("性别");
            $table->date("birth")->nullable()->comment("生日");
            $table->smallInteger("nation")->nullable()->comment("民族");
            $table->string("birthplace")->nullable()->comment("籍贯");
            $table->string("address")->nullable()->comment("家庭地址");
            $table->string("id_card")->nullable()->comment("证件号码");
            $table->string("educational")->nullable()->comment("学历");
            $table->string("science")->nullable()->comment("学位");
            $table->string("emergency_contact")->nullable()->comment("紧急联系人");
            $table->string("emergency_telephone")->nullable()->comment("紧急联系电话");
            $table->smallInteger("skill_level")->nullable()->comment("综合技能等级");
            $table->smallInteger("career_level")->nullable()->comment("职业等级");
            $table->json("vocational_skills")->nullable()->comment("职业技能");
            $table->date("attend_date")->nullable()->comment("参加工作时间");
            $table->date("entry_date")->nullable()->comment("入职时间");
            $table->timestamps();
            $table->comment = "个人信息表";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
