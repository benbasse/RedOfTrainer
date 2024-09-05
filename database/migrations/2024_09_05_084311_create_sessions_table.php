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
        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('date'); 
            $table->string('address'); 
            $table->integer('duration'); 
            $table->integer('number_of_trainees'); 
            $table->boolean('human_verification')->default(false); // Vérification humaine
            $table->text('trainer_verification_notes')->nullable(); // Notes pour le formateur par AI
            $table->integer('total_hours'); 
            // $table->enum('session_status', ['Planifiée', 'En cours', 'Terminée', 'Annulée']); // Statut de la session
            $table->string('attendance_sheet_id'); 
            $table->boolean('manual_validation')->default(false); 
            $table->foreignIdFor(User::class);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
