<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('distributeur_id')->nullable()->constrained('distributeurs')->onDelete('cascade');
            $table->string('numero_destinataire')->nullable();
            $table->enum('type', ['envoi', 'retrait', 'paiement', 'depot']);
            $table->decimal('montant', 15, 2);
            $table->timestamp('date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->enum('etat', ['encours', 'effectue', 'annule']);
            $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
