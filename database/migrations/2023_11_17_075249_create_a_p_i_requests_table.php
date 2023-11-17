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
        Schema::create('a_p_i_requests', function (Blueprint $table) {
            $table->id();
            $table->string('endpoint');
            $table->string("ip_address");
            $table->string('method');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('request_description')->nullable();
            $table->json('request')->nullable();
            $table->json('paramaeters')->nullable();
            $table->softDeletes();
            $table->json('response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('a_p_i_requests');
    }
};
