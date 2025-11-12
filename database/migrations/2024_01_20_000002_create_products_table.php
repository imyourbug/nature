<?php

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
            $table->string('name');
            $table->string('brand', 100);
            $table->decimal('price', 15, 2);
            $table->json('specs')->nullable();
            $table->enum('status', ['active', 'inactive', 'out_of_stock'])->default('active');
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->onDelete('set null');
            $table->timestamps();

            // Indexes
            $table->index('name');
            $table->index('brand');
            $table->index('status');
            $table->index('price');

            // Fulltext index for search (MySQL only)
            if (config('database.default') === 'mysql') {
                $table->fullText(['name', 'brand']);
            }
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
