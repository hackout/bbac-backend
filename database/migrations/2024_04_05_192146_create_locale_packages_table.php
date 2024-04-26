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
        Schema::create('locale_packages', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment("标识");
            $table->longText('content_zh')->nullable()->comment('中文');
            $table->longText('content_en')->nullable()->comment('英文');
            $table->timestamps();
            $table->comment = "语言记录表";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locale_packages');
    }
};
