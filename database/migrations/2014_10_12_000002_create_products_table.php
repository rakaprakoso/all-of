<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->double('price')->nullable();
            $table->double('discount_price')->nullable();
            $table->string('slug')->nullable();
            $table->string('weight')->nullable();
            $table->string('dimension')->nullable();
            $table->text('short_description')->nullable();
            $table->text('description')->nullable();
            $table->string('thumbnail_img')->nullable();
            $table->boolean('preview')->nullable()->default(true);
            $table->boolean('hero')->nullable()->default(false);
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
        Schema::dropIfExists('products');
    }
}
