<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Change Number
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->biginteger('sales_id')->unsigned()->nullable();
            // $table->foreign('sales_id')->references('id')->on('admins')->onDelete('cascade');
            $table->string('order_id')->nullable();
            $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade');
            $table->float('amount')->nullable();
            $table->string('status')->nullable();
            $table->string('type')->nullable();
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
        Schema::dropIfExists('commissions');
    }
}
