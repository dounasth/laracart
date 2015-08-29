<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('cart_categories', function($table) {
            $table->bigIncrements('id');
            $table->string('title', 255);
            $table->string('slug', 255);
            $table->text('description');
            $table->string('status', 1);
            $table->timestamps();
            $table->softDeletes();
            \Kalnoy\Nestedset\NestedSet::columns($table);

            $table->index('title');
            $table->index('slug');
            $table->index('status');
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
