<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formations', function (Blueprint $table) {
            $table->id();
            $table->string('formationName');
            $table->string('url')->nullable();
            $table->unsignedBigInteger('user_id'); // corrigé ici
            $table->timestamps();

            $table->foreign('user_id') // corrigé ici
            ->references('id')
            ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formations');
    }
};
