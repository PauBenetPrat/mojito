<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTraceAbilityToStocks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lots', function (Blueprint $table) {
            $table->increments('id');

            $table->datetime("expiration_date")->nullable();
            $table->integer("stock_id")->unsigned();

            $table->foreign('stock_id')->references('id')->on('stocks')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('serial_numbers', function (Blueprint $table) {
            $table->increments('id');
            $table->datetime("expiration_date")->nullable();
            $table->integer("stock_id")->unsigned();
            $table->integer("serial_number")->unsigned()->nullable();
            $table->integer("product_id")->unsigned();// needed?
            $table->integer("purchase_order_content_id")->unsigned()->nullable();

            $table->foreign('stock_id')->references('id')->on('stocks')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('purchase_order_content_id')->references('id')->on('purchase_order_contents')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('purchase_order_contents_lots', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("lot_id")->unsigned();
            $table->integer("purchase_order_content_id")->unsigned();

            $table->foreign('lot_id')->references('id')->on('lots')->onDelete('cascade');
            $table->foreign('purchase_order_content_id')->references('id')->on('purchase_order_contents')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table(config('mojito.itemsTable'), function (Blueprint $table) {
            $table->boolean('traceability')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lots');
        Schema::dropIfExists('serial_numbers');
        Schema::dropIfExists('purchase_order_contents_lots');
    }
}
