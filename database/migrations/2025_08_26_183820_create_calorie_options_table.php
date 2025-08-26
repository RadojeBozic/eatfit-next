<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('calorie_options', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED — poklapa se sa foreignId
            $table->unsignedSmallInteger('kcal');
            $table->json('label')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('calorie_options'); }
};
