<?php

use App\Models\productOption;
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
        Schema::create('stock_controls', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /* INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES (1, 'ROQI DESIGN', 'ayoub@gmail.com', NULL, '$2y$10$da57/q99iOOTUell4BMB9O.JGg1aRCveCB6bNSv6xd/qL7nn6afd6', NULL, '2023-10-30 09:18:39', '2023-10-30 09:18:39');
 */
    public function down(): void
    {
        Schema::dropIfExists('stock_controls');
    }
};
