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
        Schema::table('surgeries', function (Blueprint $table) {
            $table->string('responsible_assistant')->nullable()->after('surgeon_name');
            $table->enum('surgery_type', ['limpa', 'contaminada'])->default('limpa')->after('responsible_assistant');
            $table->string('procedure_type')->nullable()->after('surgery_type');
            $table->text('necessary_materials')->nullable()->after('procedure_type');
            $table->string('scheduled_by')->nullable()->after('necessary_materials');
            $table->boolean('is_elective')->default(true)->after('scheduled_by');
            $table->text('archive_reason')->nullable()->after('status');
            $table->softDeletes(); // Adiciona a coluna `deleted_at` para o arquivamento
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surgeries', function (Blueprint $table) {
            $table->dropColumn([
                'responsible_assistant',
                'surgery_type',
                'procedure_type',
                'necessary_materials',
                'scheduled_by',
                'is_elective',
                'archive_reason',
            ]);
            $table->dropSoftDeletes(); // Remove a coluna `deleted_at`
        });
    }
};