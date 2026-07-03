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
        Schema::table('generators', function (Blueprint $table) {
            $table->decimal('price_KW', 8, 2)->nullable()->after('powerKW');
        });
    }

    public function down(): void
    {
        Schema::table('generators', function (Blueprint $table) {
            $table->dropColumn('price_KW');
        });
    }
};
