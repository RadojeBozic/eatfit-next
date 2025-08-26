<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status',['draft','placed','paid','prep','dispatch','delivered','canceled'])->default('placed');
            $table->string('payment_status')->nullable();

            $table->foreignId('plan_id')->constrained('plans');
            $table->foreignId('calorie_option_id')->constrained('calorie_options');
            $table->foreignId('duration_id')->constrained('durations');
            $table->date('start_date')->nullable();

            $table->string('customer_name');
            $table->string('phone');
            $table->string('email')->nullable();

            $table->string('address_line');
            $table->string('city');
            $table->string('postal_code', 16);
            $table->foreignId('delivery_zone_id')->nullable()->constrained('delivery_zones')->nullOnDelete();
            $table->unsignedInteger('subtotal_cents')->default(0);
            $table->unsignedInteger('delivery_fee_cents')->default(0);
            $table->unsignedInteger('discount_cents')->default(0);
            $table->unsignedInteger('total_cents')->default(0);
            $table->string('currency',3)->default('RSD');

            $table->string('proforma_path')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('orders'); }
};
