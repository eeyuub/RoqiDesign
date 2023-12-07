<?php

use App\Models\Motif;
use App\Models\Product;
use App\Models\productSize;
use App\Models\Warehouse;
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
        Schema::create('product_options', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Product::class)->constrained()->nullable();
            $table->foreignIdFor(Warehouse::class)->constrained()->nullable();
            $table->foreignIdFor(productSize::class)->constrained()->nullable();
            $table->foreignIdFor(Motif::class)->constrained()->nullable();
            $table->string('code')->nullable();

            $table->string('option')->nullable();
            $table->string('attachement')->nullable();
            $table->string('note')->nullable();
            $table->float('quantity',8,2)->nullable();
            $table->float('unitPrice',8,2)->nullable();
            $table->boolean('isFactured')->default(0)->nullable();
            $table->float('qteDispo',8,2)->default(0)->nullable();
            // $table->float('totalAmount',8,2)->nullable();
            // $table->boolean('isStockable')->nullable()->default(1);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_options');
    }
};
