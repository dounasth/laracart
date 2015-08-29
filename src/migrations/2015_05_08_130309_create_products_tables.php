<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('cart_products', function($table) {
            $table->bigIncrements('id');
            $table->string('sku', 255)->unique();
            $table->string('slug', 255);
            $table->string('status', 1);
            $table->timestamps();
            $table->softDeletes();

            $table->index('sku');
            $table->index('slug');
            $table->index('status');
        });
        Schema::create('cart_product_prices', function($table) {
            $table->bigIncrements('id');
            $table->bigInteger('product_id');
            $table->decimal('price', 10, 2);
            $table->decimal('list_price', 10, 2);

            $table->index('product_id');
            $table->index('price');
            $table->index('list_price');
            $table->foreign('product_id')->references('id')->on('cart_products');
        });
        Schema::create('cart_product_descriptions', function($table) {
            $table->bigIncrements('id');
            $table->bigInteger('product_id');
            $table->text('short');
            $table->longText('full');

            $table->index('product_id');
            $table->index('short');
            $table->index('full');
            $table->foreign('product_id')->references('id')->on('cart_products');
        });
    }

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('cart_products');
        Schema::drop('cart_product_prices');
        Schema::drop('cart_product_descriptions');
	}

}
