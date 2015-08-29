<?php
/**
 * Created by PhpStorm.
 * User: nimda
 * Date: 5/22/15
 * Time: 11:49 AM
 */

namespace Bonweb\Laracart;


class ProductsCategories extends \Eloquent {

//    use \SoftDeletingTrait;

    protected $table = 'cart_products_categories';
//    protected $dates = ['deleted_at'];

    public function products()
    {
        return $this->morphMany('Bonweb\Laracart\Product', 'product', 'id', 'product_id');
    }

}