<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsCategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('cart_products_categories', function($table) {
            $table->increments('id');
            $table->bigInteger('category_id');
            $table->string('link_type', 255);
            $table->bigInteger('categorizable_id');
            $table->string('categorizable_type', 255);
            $table->timestamps();
            $table->softDeletes();

            $table->index('category_id');
            $table->index('link_type');
            $table->index('categorizable_id');
            $table->index('categorizable_type');
            $table->index('categorizable_id', 'categorizable_type');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}
