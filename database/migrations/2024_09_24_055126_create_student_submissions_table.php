<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentSubmissionsTable extends Migration
{
    public function up()
    {
        Schema::create('student_submissions', function (Blueprint $table) {
            $table->id();
            
            $table->string('name'); // Store student name
            $table->string('email'); // Store student email
            $table->string('phone')->nullable(); // Store student phone
            $table->string('cv_path'); // Path to the CV upload
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            
            $table->timestamp('accepted_at')->nullable(); // Acceptance timestamp
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_submissions');
    }
}
