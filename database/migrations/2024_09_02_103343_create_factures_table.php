<?php

use App\Models\Client;
use App\Models\Line_items;
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
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->string('facture_number')->unique();
            $table->foreignIdFor(Client::class);
            $table->foreignIdFor(User::class);
            $table->date('due_date');
            $table->integer('total_amount_ht');
            $table->integer('total_vat');
            $table->integer('total_amount_ttc');
            $table->enum('status', ['impayee', 'payee'])->default('impayee');
            $table->enum('payment_method', ['virement', 'carte_bancaire', 'cheque']);
            $table->longText('internal_notes');
            $table->string('sent_to');
            $table->boolean('auto_reminder')->default(false);
            $table->softDeletes();
            $table->timestamps();
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
