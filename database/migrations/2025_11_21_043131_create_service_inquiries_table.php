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
        Schema::create('service_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('inquiry_number', 50)->unique()->comment('Format: INQ-2025-XXXX');

            // Contact Information (Lead Data)
            $table->string('email')->index();
            $table->string('company_name');
            $table->string('company_type', 100)->nullable()->comment('PT, CV, Individual, etc');
            $table->string('phone', 50);
            $table->string('contact_person');
            $table->string('position', 100)->nullable();

            // Project/Business Information
            $table->string('kbli_code', 10)->nullable();
            $table->text('kbli_description')->nullable();
            $table->text('business_activity')->comment('User description of business');
            $table->json('form_data')->comment('All form fields in JSON format');

            // AI Analysis Results
            $table->json('ai_analysis')->nullable()->comment('AI recommendations and analysis');
            $table->string('ai_model_used', 50)->nullable()->default('gpt-3.5-turbo');
            $table->integer('ai_processing_time')->nullable()->comment('Processing time in milliseconds');
            $table->integer('ai_tokens_used')->nullable();
            $table->timestamp('analyzed_at')->nullable();

            // Lead Management
            $table->enum('status', [
                'new',
                'processing',
                'error',
                'analyzed',
                'contacted',
                'qualified',
                'converted',
                'registered',
                'lost',
            ])->default('new')->index();
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium')->index();
            $table->string('source', 50)->default('landing_page');
            $table->string('utm_source', 100)->nullable();
            $table->string('utm_medium', 100)->nullable();
            $table->string('utm_campaign', 100)->nullable();

            // Conversion Tracking
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null');
            $table->foreignId('converted_to_application_id')->nullable()->constrained('permit_applications')->onDelete('set null');
            $table->timestamp('converted_at')->nullable();

            // Security & Tracking
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('session_id')->nullable();

            // Follow-up
            $table->timestamp('last_contacted_at')->nullable();
            $table->foreignId('contacted_by')->nullable()->constrained('users')->onDelete('set null')->comment('Admin user who contacted');
            $table->text('admin_notes')->nullable();

            $table->timestamps();

            // Additional indexes for performance
            $table->index('created_at');
            $table->index(['status', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_inquiries');
    }
};
