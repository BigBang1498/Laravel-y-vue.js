<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->index(['department_id', 'direction', 'status'], 'idx_docs_dept_dir_status');
            $table->index(['direction', 'received_or_sent_at'], 'idx_docs_dir_received_at');
            $table->index(['due_date', 'status'], 'idx_docs_due_status');
        });

        Schema::table('assignments', function (Blueprint $table) {
            $table->index(['assigned_to_user_id', 'is_current'], 'idx_asgn_user_current');
            $table->index(['assigned_to_department_id', 'is_current'], 'idx_asgn_dept_current');
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->index(['user_id', 'event'], 'idx_audit_user_event');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex('idx_docs_dept_dir_status');
            $table->dropIndex('idx_docs_dir_received_at');
            $table->dropIndex('idx_docs_due_status');
        });

        Schema::table('assignments', function (Blueprint $table) {
            $table->dropIndex('idx_asgn_user_current');
            $table->dropIndex('idx_asgn_dept_current');
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex('idx_audit_user_event');
        });
    }
};
