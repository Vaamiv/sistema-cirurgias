<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
       Schema::create('surgeries', function (Blueprint $table) {
    $table->id();
    $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
    $table->string('surgeon_name');
    $table->dateTime('start_at');   // <- era timestamp
    $table->dateTime('end_at');     // <- era timestamp
    $table->enum('status', ['agendada','confirmada','em_andamento','finalizada','adiada','cancelada'])->default('agendada');
    $table->timestamps();
    $table->index(['start_at','end_at']);
});

    }
    public function down(): void { Schema::dropIfExists('surgeries'); }
};
