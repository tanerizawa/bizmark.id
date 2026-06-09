<?php

use Illuminate\Support\Facades\Route;

// Lead Management Routes
Route::middleware('permission:clients.view')->group(function () {
    Route::get('leads', [App\Http\Controllers\Admin\LeadManagementController::class, 'index'])->name('admin.leads.index');

    Route::get('service-inquiries/index', function () {
        return redirect('/admin/leads?tab=service-inquiries');
    })->name('admin.service-inquiries.index');

    // Show route must be defined before the base redirect to ensure it takes priority
    Route::get('service-inquiries/export', [App\Http\Controllers\Admin\ServiceInquiryController::class, 'export'])->name('admin.service-inquiries.export');
    Route::get('service-inquiries/{serviceInquiry}', [App\Http\Controllers\Admin\ServiceInquiryController::class, 'show'])->name('admin.service-inquiries.show');
    Route::patch('service-inquiries/{serviceInquiry}/status', [App\Http\Controllers\Admin\ServiceInquiryController::class, 'updateStatus'])
        ->middleware('permission:clients.edit')
        ->name('admin.service-inquiries.update-status');
    Route::patch('service-inquiries/{serviceInquiry}/priority', [App\Http\Controllers\Admin\ServiceInquiryController::class, 'updatePriority'])
        ->middleware('permission:clients.edit')
        ->name('admin.service-inquiries.update-priority');
    Route::post('service-inquiries/{serviceInquiry}/note', [App\Http\Controllers\Admin\ServiceInquiryController::class, 'addNote'])
        ->middleware('permission:clients.edit')
        ->name('admin.service-inquiries.add-note');
    Route::post('service-inquiries/{serviceInquiry}/convert', [App\Http\Controllers\Admin\ServiceInquiryController::class, 'convertToProject'])
        ->middleware('permission:projects.create')
        ->name('admin.service-inquiries.convert');
    Route::delete('service-inquiries/{serviceInquiry}', [App\Http\Controllers\Admin\ServiceInquiryController::class, 'destroy'])
        ->middleware('permission:clients.delete')
        ->name('admin.service-inquiries.destroy');
    Route::post('service-inquiries/{serviceInquiry}/send-result', [App\Http\Controllers\Admin\ServiceInquiryController::class, 'sendResult'])
        ->middleware('permission:clients.edit')
        ->name('admin.service-inquiries.send-result');

    Route::get('consultation-leads', [App\Http\Controllers\Admin\ConsultationLeadController::class, 'index'])->name('admin.consultation-leads.index');
    Route::get('consultation-leads/export', [App\Http\Controllers\Admin\ConsultationLeadController::class, 'export'])->name('admin.consultation-leads.export');
    Route::get('consultation-leads/{consultation}', [App\Http\Controllers\Admin\ConsultationLeadController::class, 'show'])->name('admin.consultation-leads.show');
    Route::post('consultation-leads/{consultation}/update-status', [App\Http\Controllers\Admin\ConsultationLeadController::class, 'updateStatus'])
        ->middleware('permission:clients.edit')
        ->name('admin.consultation-leads.update-status');
    Route::post('consultation-leads/{consultation}/mark-contacted', [App\Http\Controllers\Admin\ConsultationLeadController::class, 'markContacted'])
        ->middleware('permission:clients.edit')
        ->name('admin.consultation-leads.mark-contacted');
    Route::post('consultation-leads/{consultation}/convert', [App\Http\Controllers\Admin\ConsultationLeadController::class, 'convertToClient'])
        ->middleware('permission:clients.create')
        ->name('admin.consultation-leads.convert-to-client');
    Route::post('consultation-leads/{consultation}/note', [App\Http\Controllers\Admin\ConsultationLeadController::class, 'addNote'])
        ->middleware('permission:clients.edit')
        ->name('admin.consultation-leads.add-note');
    Route::delete('consultation-leads/{consultation}', [App\Http\Controllers\Admin\ConsultationLeadController::class, 'destroy'])
        ->middleware('permission:clients.delete')
        ->name('admin.consultation-leads.destroy');

    Route::get('service-cost-requests/{requestNumber}', [App\Http\Controllers\Admin\ServiceCostRequestController::class, 'show'])->name('admin.service-cost-requests.show');
    Route::patch('service-cost-requests/{requestNumber}/status', [App\Http\Controllers\Admin\ServiceCostRequestController::class, 'updateStatus'])
        ->middleware('permission:clients.edit')
        ->name('admin.service-cost-requests.update-status');
    Route::post('service-cost-requests/{requestNumber}/note', [App\Http\Controllers\Admin\ServiceCostRequestController::class, 'addNote'])
        ->middleware('permission:clients.edit')
        ->name('admin.service-cost-requests.add-note');
    Route::post('service-cost-requests/{requestNumber}/generate-quote', [App\Http\Controllers\Admin\ServiceCostRequestController::class, 'generateQuote'])
        ->middleware('permission:invoices.create')
        ->name('admin.service-cost-requests.generate-quote');
    Route::post('service-cost-requests/{requestNumber}/regenerate-content', [App\Http\Controllers\Admin\ServiceCostRequestController::class, 'regenerateQuoteContent'])
        ->middleware('permission:invoices.edit')
        ->name('admin.service-cost-requests.regenerate-content');
    Route::post('service-cost-requests/{requestNumber}/send-email', [App\Http\Controllers\Admin\ServiceCostRequestController::class, 'sendQuoteEmail'])
        ->middleware('permission:email.send_email')
        ->name('admin.service-cost-requests.send-email');
    Route::post('service-cost-requests/{requestNumber}/complete', [App\Http\Controllers\Admin\ServiceCostRequestController::class, 'complete'])
        ->middleware('permission:clients.edit')
        ->name('admin.service-cost-requests.complete');
    Route::post('service-cost-requests/{requestNumber}/archive', [App\Http\Controllers\Admin\ServiceCostRequestController::class, 'archive'])
        ->middleware('permission:clients.edit')
        ->name('admin.service-cost-requests.archive');

    // Base index redirect — only matches exact /admin/service-inquiries, never /admin/service-inquiries/{id}
    Route::get('service-inquiries', function () {
        return redirect('/admin/leads?tab=service-inquiries');
    });
});
