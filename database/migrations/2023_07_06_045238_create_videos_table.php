<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->integer('category_id')->nullable();
            $table->string('cover_photo')->nullable();
            $table->string('video_path')->nullable();
            $table->string('duration')->nullable();
            $table->integer('status');
            $table->integer('deleted_by')->nullable();
            $table->integer('published_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('videos');
    }
}
