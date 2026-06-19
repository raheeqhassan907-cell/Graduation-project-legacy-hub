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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('specialty'); // التخصص
            $table->string('technologies')->nullable(); // التقنيات المستخدمة (comma-separated tags)
            $table->string('file_url')->nullable(); // ملف المشروع PDF
            $table->integer('year'); // سنة المشروع
            $table->foreignId('graduate_id')->nullable()->constrained('users')->onDelete('set null'); // الخريج صاحب المشروع
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->onDelete('set null'); // الأستاذ المشرف
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
