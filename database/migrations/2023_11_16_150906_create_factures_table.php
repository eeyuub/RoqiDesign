<?php

use App\Models\Customer;
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
        Schema::create('factures', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Customer::class)->constrained()->nullable();

            // $table->string('orderStatus')->nullable();
            // $table->string('orderPayment')->nullable();
            $table->string('numeroFacture')->nullable();
            $table->string('note')->nullable();
            $table->float('totalHT',8,2)->nullable();
            $table->float('tva',8,2)->nullable();
            $table->float('remise',8,2)->nullable();
            $table->float('totalTTC',8,2)->nullable();
            $table->date('factureDate')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
