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
        Schema::create('sub_counties', function (Blueprint $table) {
            $table->id();
            //districtCode is a foreign key referencing the district table
            $table->foreignId('districtCode')->constrained('districts');
            //countyCode is a foreign key referencing the county table
            $table->foreignId('countyCode')->constrained('counties');
            $table->string('subCountyCode');
            $table->string('subCountyName');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_counties');
    }
};
