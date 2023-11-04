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
        Schema::create('villages', function (Blueprint $table) {
            $table->id();
            //districtCode is a foreign key referencing the district table
            $table->foreignId('districtCode')->constrained('districts');
            //countyCode is a foreign key referencing the county table
            $table->foreignId('countyCode')->constrained('counties');
            //subCountyCode is a foreign key referencing the sub_county table
            $table->foreignId('subCountyCode')->constrained('sub_counties');
             //parishCode is a foreign key referencing the parish table
            $table->foreignId('parishCode')->constrained('parishes');
            $table->string('villageCode');
            $table->string('villageName');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('villages');
    }
};
