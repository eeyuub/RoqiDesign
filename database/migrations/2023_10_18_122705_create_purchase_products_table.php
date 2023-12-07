<?php

use App\Models\productOption;
use App\Models\Purchase;
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
        Schema::create('purchase_products', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Purchase::class)->constrained()->nullable();
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


    public function down(): void
    {
        Schema::dropIfExists('purchase_products');
    }
};
