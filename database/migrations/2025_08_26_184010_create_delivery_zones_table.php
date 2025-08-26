<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED
            $table->json('name');
            $table->unsignedInteger('fee_cents')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('delivery_zones'); }
};
