<?php

use App\Models\Facture;
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
        Schema::create('line_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('date');
            $table->longText('description');
            $table->integer('unit_price_ht');
            $table->integer('vat');
            $table->integer('unit_price_ttc');
            $table->integer('discount');
            $table->integer('line_total_ht');
            $table->foreignIdFor(Facture::class);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('line_items');
    }
};
