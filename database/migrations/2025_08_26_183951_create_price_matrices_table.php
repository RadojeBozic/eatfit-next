<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('price_matrices', function (Blueprint $table) {
    $table->id();
    $table->foreignId('plan_id')->constrained('plans');
    $table->foreignId('calorie_option_id')->constrained('calorie_options');
    $table->foreignId('duration_id')->constrained('durations');
    $table->unsignedInteger('price_cents');
    $table->string('currency', 3)->default('RSD');
    $table->date('active_from')->nullable();
    $table->date('active_to')->nullable();
    $table->timestamps();
    $table->unique(['plan_id','calorie_option_id','duration_id','active_from'],'pm_unique');
});
    }
    public function down(): void { Schema::dropIfExists('price_matrices'); }
};
