<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('post_author')->nullable();
            $table->string('post_title')->nullable();
            $table->string('post_image_thumbnail')->nullable();
            $table->string('post_slug')->nullable();
            $table->string('post_type')->nullable();
            $table->boolean('is_publish')->nullable();
            $table->longText('post_content')->nullable();
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
        Schema::dropIfExists('posts');
    }
}
