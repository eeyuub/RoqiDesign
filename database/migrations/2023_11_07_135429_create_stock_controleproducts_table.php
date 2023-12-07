<?php

use App\Models\productOption;
use App\Models\stockControl;
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
        Schema::create('stock_controleproducts', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(stockControl::class)->constrained()->nullable();
            $table->foreignIdFor(productOption::class)->constrained()->nullable();

            $table->float('quantity',8,2)->nullable();
            $table->float('unitPrice',8,2)->nullable();
            $table->float('totalAmount',8,2)->nullable();
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
        Schema::dropIfExists('stock_controleproducts');
    }
};
