<?php

use App\Models\Customer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Customer::class)->constrained()->nullable();

            $table->string('orderStatus')->nullable();
            $table->string('orderPayment')->nullable();
            $table->string('orderNumber')->nullable();
            $table->float('totalAmount',8,2)->nullable();
            $table->string('note')->nullable();
            $table->date('orderDate')->nullable();
            $table->date('shippedDate')->nullable();
            $table->date('deliveredDate')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
