<?php

use App\Models\Customer;
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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Supplier::class)->constrained()->nullable();
            $table->date('purchaseDate')->nullable();
            $table->string('purchaseStatus')->nullable();
            $table->string('purchasePayment')->nullable();
            $table->string('purchaseNumber')->nullable();
            $table->float('totalAmount',8,2)->nullable();
            $table->string('note')->nullable();
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
        Schema::dropIfExists('purchases');
    }
};
