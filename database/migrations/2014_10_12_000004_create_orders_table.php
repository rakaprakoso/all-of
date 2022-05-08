<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('order_id')->nullable()->unique();
            $table->dateTime('order_date')->nullable();
            $table->dateTime('shipped_date')->nullable();
            $table->dateTime('confirm_date')->nullable();
            $table->string('name_buyer')->nullable();
            $table->string('email_buyer')->nullable();
            $table->string('phone_buyer')->nullable();
            $table->text('address_buyer')->nullable();
            $table->text('shipping_address_buyer')->nullable();
            $table->string('shipping_method')->nullable();
            $table->string('shipping_cost')->nullable();
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
        Schema::dropIfExists('order_masters');
    }
}
