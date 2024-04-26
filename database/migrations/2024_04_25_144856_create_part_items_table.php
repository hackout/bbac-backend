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
        Schema::create('part_items', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique()->comment("主键");
            $table->uuid('part_id')->nullable()->comment('零件ID');
            $table->uuid('assembly_id')->nullable()->comment('总成ID');
            $table->uuid('user_id')->nullable()->comment('用户ID');
            $table->uuid('product_id')->nullable()->comment('发动机ID');
            $table->string("number")->unique()->comment("零件编号");
            $table->timestamps();
            $table->comment = "零件子项记录表";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('part_items');
    }
};
