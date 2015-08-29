<?php

namespace Bonweb\Laradmin;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ProductMeta extends Eloquent
{
	/**
	 * The table name for t his model.
	 *
	 * @var string
	 */
	protected $table = 'cart_product_meta';
	
	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = array('id');
	
}
