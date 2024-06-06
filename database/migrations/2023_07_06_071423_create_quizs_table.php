<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quizs', function (Blueprint $table) {
            $table->id();
            $table->text('question')->nullable();
            $table->integer('quiz_type')->nullable();
            $table->integer('content_type')->nullable();
            $table->integer('status');
            $table->integer('deleted_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('published_by')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->integer('deactivated_by')->nullable();
            $table->timestamp('deactivated_at')->nullable(); 
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quizs');
    }
}
