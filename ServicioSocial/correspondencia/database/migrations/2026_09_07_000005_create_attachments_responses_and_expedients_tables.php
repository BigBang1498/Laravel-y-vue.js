<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            $table->foreignId('uploaded_by')->constrained('users')->restrictOnDelete();
            $table->string('original_name');
            $table->string('file_path', 500);
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('file_size');
            $table->string('file_hash', 64)->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            $table->foreignId('sent_document_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->text('response_text');
            $table->dateTime('response_date');
            $table->timestamps();
        });

        Schema::create('expedients', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('department_id')->constrained('departments')->restrictOnDelete();
            $table->foreignId('responsible_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 30)->default('open')->index();
            $table->date('opened_at');
            $table->date('closed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('expedient_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expedient_id')->constrained('expedients')->cascadeOnDelete();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            $table->foreignId('added_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['expedient_id', 'document_id']);
        });

        Schema::create('document_relationships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            $table->foreignId('related_document_id')->constrained('documents')->cascadeOnDelete();
            $table->string('relation_type', 50);
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['document_id', 'related_document_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_relationships');
        Schema::dropIfExists('expedient_documents');
        Schema::dropIfExists('expedients');
        Schema::dropIfExists('responses');
        Schema::dropIfExists('attachments');
    }
};
