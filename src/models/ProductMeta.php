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
	public $table = 'cart_product_meta';
	
	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = array('id');

	public static function getTableName() {
		$t = new ProductMeta();
		return $t->getTable();
	}

	public static function rawdb(){
		$t = new ProductMeta();
		return \DB::table($t->table);
	}

	public static function productsWithMeta($meta, $activeOnly=true){
		$q = "SELECT xref_id FROM cart_product_meta";
		$w = [];
		foreach ($meta as $mid => $mval) {
			$mval = str_ireplace("'", "\'", $mval );
			$w[] = $q . " WHERE (meta_id = $mid AND value = '$mval')";
		}
		if ($activeOnly) {
			$w[] = $q . " WHERE xref_id IN ( SELECT id FROM cart_products WHERE STATUS = 'A')";
		}
		return $w;
	}

}
