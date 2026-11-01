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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 20)->unique();
            $table->string('level', 3);
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->enum('role', ['user', 'superadmin'])->default('user');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->string('name');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->date('report_date');
            $table->enum('status', ['draft', 'terkirim', 'diterima', 'perlu_perbaikan'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->text('verification_note')->nullable();
            $table->unique(['class_id', 'report_date']);
            $table->timestamps();
        });

        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_report_id')->constrained('daily_reports')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->enum('duha', ['hadir', 'tidak_hadir', 'munfarid', 'haid', 'tidak_sholat', 'tidak_sekolah'])->nullable();
            $table->enum('dzuhur', ['hadir', 'tidak_hadir', 'haid', 'tidak_sholat', 'tidak_sekolah'])->nullable();
            $table->enum('quran', ['bawa', 'tidak_bawa', 'tidak_sekolah'])->nullable();
            $table->enum('tadarus', ['ikut', 'tidak'])->nullable();
            $table->smallInteger('duha_score')->default(0);
            $table->smallInteger('dzuhur_score')->default(0);
            $table->smallInteger('quran_score')->default(0);
            $table->smallInteger('tadarus_score')->default(0);
            $table->smallInteger('total_score')->default(0);
            $table->unique(['daily_report_id', 'student_id']);
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('value');
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('assessments');
        Schema::dropIfExists('daily_reports');
        Schema::dropIfExists('students');
        Schema::dropIfExists('users');
        Schema::dropIfExists('classes');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
