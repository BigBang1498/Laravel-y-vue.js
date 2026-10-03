<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('folio_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('prefix', 20);
            $table->unsignedSmallInteger('year');
            $table->unsignedBigInteger('current_number')->default(0);
            $table->timestamps();

            $table->unique(['prefix', 'year']);
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('direction', 20)->index();
            $table->string('folio', 35)->unique();
            $table->string('external_number', 100)->nullable()->index();
            $table->foreignId('document_type_id')->constrained('document_types')->restrictOnDelete();
            $table->string('sender_name', 255);
            $table->string('sender_institution', 255)->nullable();
            $table->string('recipient_name', 255);
            $table->string('recipient_institution', 255)->nullable();
            $table->string('subject', 500);
            $table->text('description')->nullable();
            $table->date('document_date');
            $table->dateTime('received_or_sent_at');
            $table->foreignId('department_id')->constrained('departments')->restrictOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('current_assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('priority', 20)->default('normal')->index();
            $table->string('status', 30)->default('received')->index();
            $table->date('due_date')->nullable()->index();
            $table->dateTime('attended_at')->nullable();
            $table->dateTime('closed_at')->nullable();
            $table->string('security_level', 20)->default('public');
            $table->text('observations')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
        Schema::dropIfExists('folio_sequences');
    }
};
