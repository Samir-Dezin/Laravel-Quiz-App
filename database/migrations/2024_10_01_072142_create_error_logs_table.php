<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateErrorLogsTable extends Migration
{
    public function up()
    {
        Schema::create('error_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('request_logs')->onDelete('cascade'); // Foreign key to request_logs
            $table->string('error_message');
            $table->string('error_file');
            $table->integer('error_line');
            $table->text('error_trace');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('error_logs');
    }
}
