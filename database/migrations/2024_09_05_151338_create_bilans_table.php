<?php

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
        Schema::create('bilans', function (Blueprint $table) {
            $table->id();
            $table->year('year');
            $table->integer('total_invoices_ht');
            $table->integer('total_session_hours');
            $table->integer('total_session');
            $table->integer('total_trainees');
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bilans');
    }
};
