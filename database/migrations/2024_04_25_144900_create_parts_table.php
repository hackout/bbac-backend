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
        Schema::create('parts', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique()->comment("主键");
            $table->string("name")->comment('零件名称');
            $table->string("name_en")->nullable()->comment('零件名称(英)');
            $table->string("station")->nullable()->comment('工位号');
            $table->string("number")->unique()->comment("零件编号");
            $table->boolean('is_esd')->default(false)->comment('是否ESD');
            $table->boolean('is_traceability')->default(false)->comment('是否追踪件');
            $table->boolean('is_one_time')->default(false)->comment('是否OneTime');
            $table->timestamps();
            $table->comment = "零件清单";
        });
        Schema::create('parts_assemblies', function (Blueprint $table) {
            $table->uuid('part_id');
            $table->uuid('assembly_id');
            $table->integer("num")->default(0);
            $table->primary(['part_id', 'assembly_id'], 'part_assembly');
            $table->comment = "零件总成关联";
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parts');
        Schema::dropIfExists('parts_assemblies');
    }
};
