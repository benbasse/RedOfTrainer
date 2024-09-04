<?php

use App\Models\Client;
use App\Models\User;
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
        Schema::create('devis', function (Blueprint $table) {
            $table->id();
            $table->string('devis_number');
            $table->foreignIdFor(Client::class);
            $table->foreignIdFor(User::class);
            $table->date('due_date');
            $table->integer('total_amount_ht');
            $table->integer('total_vat');
            $table->integer('total_amount_ttc');
            $table->longText('special_conditions');
            $table->longText('internal_notes');
            $table->string('sent_to');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devis');
    }
};
