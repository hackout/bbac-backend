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
        Schema::create('dict_items', function (Blueprint $table) {
            $table->id();
            $table->integer("dict_id")->index()->comment("字典ID");
            $table->string("name")->comment("标签");
            $table->integer("content")->comment("键值");
            $table->integer("sort_order")->default(0)->comment("排序");
            $table->timestamps();
            $table->comment = "数据字典键值表";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dict_items');
    }
};
