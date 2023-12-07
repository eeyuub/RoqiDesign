<?php

use App\Models\Order;
use App\Models\productOption;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Unique;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_products', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Order::class)->constrained()->nullable();
            $table->foreignIdFor(productOption::class)->constrained()->nullable();

            // $table->string('code')->unique()->nullable();
            // $table->string('option')->nullable();
            // $table->string('attachement')->nullable();
            $table->float('quantity',8,2)->nullable();
            $table->float('unitPrice',8,2)->nullable();
            $table->float('totalAmount',8,2)->nullable();
            // $table->boolean('isStockable')->nullable()->default(1);
            $table->string('note')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_products');
    }
};
