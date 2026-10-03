<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\ExpedientController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReceivedDocumentController;
use App\Http\Controllers\RecipientController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SenderController;
use App\Http\Controllers\SentDocumentController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('received-documents', ReceivedDocumentController::class)->except(['show', 'destroy']);
    Route::resource('sent-documents', SentDocumentController::class)->except(['show', 'destroy']);

    Route::get('documents/{document}', [DocumentController::class, 'show'])->name('documents.show');
    Route::patch('documents/{document}/change-status', [DocumentController::class, 'changeStatus'])->name('documents.change-status');
    Route::post('documents/{document}/assignments', [AssignmentController::class, 'store'])->name('documents.assignments.store');
    Route::get('departments/{department}/users', [AssignmentController::class, 'getUsersByDepartment'])->name('departments.users');

    Route::post('documents/{document}/attachments', [AttachmentController::class, 'store'])->name('documents.attachments.store');
    Route::get('documents/{document}/attachments/{attachment}/download', [AttachmentController::class, 'download'])->name('documents.attachments.download');
    Route::delete('documents/{document}/attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('documents.attachments.destroy');

    Route::post('documents/{document}/responses', [ResponseController::class, 'store'])->name('documents.responses.store');

    Route::resource('expedients', ExpedientController::class);
    Route::post('expedients/{expedient}/add-document', [ExpedientController::class, 'addDocument'])->name('expedients.add-document');
    Route::delete('expedients/{expedient}/documents/{document}', [ExpedientController::class, 'removeDocument'])->name('expedients.remove-document');

    Route::get('search/advanced', [SearchController::class, 'advanced'])->name('search.advanced');

    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::match(['post', 'patch'], 'notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export-csv', [ReportController::class, 'exportCsv'])->name('reports.export-csv');
    Route::get('reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');
    Route::get('reports/print-summary', [ReportController::class, 'printSummary'])->name('reports.print-summary');
    Route::get('documents/{document}/slip', [ReportController::class, 'documentSlip'])->name('documents.slip');
    Route::get('documents/{document}/print-slip', [ReportController::class, 'printSlip'])->name('documents.print-slip');

    Route::prefix('tracking')->name('tracking.')->group(function () {
        Route::get('my-documents', [TrackingController::class, 'myDocuments'])->name('my-documents');
        Route::get('pending', [TrackingController::class, 'pending'])->name('pending');
        Route::get('expired', [TrackingController::class, 'expired'])->name('expired');
    });

    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::patch('roles/{role}/toggle-status', [RoleController::class, 'toggleStatus'])->name('roles.toggle-status');
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

        Route::resource('departments', DepartmentController::class);
        Route::patch('departments/{department}/toggle-status', [DepartmentController::class, 'toggleStatus'])->name('departments.toggle-status');

        Route::resource('document-types', DocumentTypeController::class);
        Route::patch('document-types/{document_type}/toggle-status', [DocumentTypeController::class, 'toggleStatus'])->name('document-types.toggle-status');

        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show');
    });

    Route::middleware('role:admin,recepcion,secretaria,jefe_departamento')->group(function () {
        Route::resource('senders', SenderController::class);
        Route::resource('recipients', RecipientController::class);
    });
});
