<?php

use App\Models\Order;
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
        Schema::create('order_extends', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Order::class)->constrained()->nullable();
            // $table->foreignIdFor(productSize::class)->constrained()->nullable();
            $table->float('quantity',8,2)->nullable();
            $table->float('unitPrice',8,2)->nullable();
            $table->float('totalAmount',8,2)->nullable();
            $table->string('productSize')->nullable();
            $table->string('designation')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_extends');
    }
};
