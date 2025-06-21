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
        Schema::create('principals', function (Blueprint $table) {
            $table->id();
            $table->uuid('principalId')->unique();
            $table->foreignId('creator_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('checker_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('approver_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->string('name');
            $table->string('category');
            $table->string('other_category')->nullable();
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('address');
            $table->string('product_name');
            $table->string('payment_type');
            $table->string('payment_type_name')->nullable();
            $table->string('type');
            $table->enum('domestic_nib_status', ['none', 'available']);
            $table->string('domestic_nib')->nullable();
            $table->enum('domestic_certificate_status', ['none', 'available']);
            $table->string('domestic_certificate')->nullable();
            $table->enum('domestic_related_documents_status', ['none', 'available']);
            $table->json('domestic_related_documents')->nullable(); // JSON to store related documents
            $table->enum('international_quality_certification_status', ['none', 'available']);
            $table->json('international_quality_certification')->nullable();
            $table->enum('international_safety_certification_status', ['none', 'available']);
            $table->json('international_safety_certification')->nullable();
            $table->json('principal_checklist');
            $table->enum('conclusion', ['recommended', 'not_recommended']);
            $table->text('follow_up_plan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('principals');
    }
};
