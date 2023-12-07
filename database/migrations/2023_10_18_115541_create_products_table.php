<?php

use App\Models\Category;
use App\Models\Supplier;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('note')->nullable();
            $table->string('description')->nullable();
            $table->foreignIdFor(Category::class)->constrained()->nullable();
            $table->foreignIdFor(Supplier::class)->constrained()->nullable();
            $table->string('attachement')->nullable();
            // $table->boolean('isStockable')->nullable()->default(1);
            $table->boolean('isInvoiced')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
