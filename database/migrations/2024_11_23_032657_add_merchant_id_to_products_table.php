<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMerchantIdToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('merchant_id')->nullable()->after('id'); // Add the merchant_id column
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade'); // Add foreign key
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['merchant_id']); // Drop the foreign key
            $table->dropColumn('merchant_id');   // Drop the column
        });
    }
}
