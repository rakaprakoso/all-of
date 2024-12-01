<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderStatusIdToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('order_status_id')->after('shipping_cost')->nullable(); // Add the column
            $table->foreign('order_status_id')->references('id')->on('order_statuses')->onDelete('cascade'); // Add foreign key
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['order_status_id']); // Drop the foreign key
            $table->dropColumn('order_status_id');   // Drop the column
        });
    }
}
