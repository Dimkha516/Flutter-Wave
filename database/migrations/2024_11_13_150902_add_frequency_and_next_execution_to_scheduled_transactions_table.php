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
        Schema::table('scheduled_transactions', function (Blueprint $table) {
            $table->enum('frequence', ['daily', 'weekly', 'monthly'])->default('monthly');
            $table->date('prochaine_execution')->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scheduled_transactions', function (Blueprint $table) {
            $table->dropColumn('frequence');
            $table->dropColumn('prochaine_execution');
        });
    }
};
