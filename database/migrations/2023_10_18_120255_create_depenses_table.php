<?php

use App\Models\depenseItem;
use App\Models\provider;
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
        Schema::create('depenses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(depenseItem::class)->constrained()->nullable();
            $table->foreignIdFor(provider::class)->constrained()->nullable();
            $table->string('note')->nullable();
            $table->float('quantity',8,2)->nullable();
            $table->float('unitPrice',8,2)->nullable();
            $table->string('source')->nullable();
            $table->float('totalAmount',8,2)->nullable();
            $table->date('datePurchase')->nullable();
            $table->string('attachement')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('depenses');
    }
};
