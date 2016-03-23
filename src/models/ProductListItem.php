<?php

namespace Bonweb\Laracart;

class ProductListItem extends \Illuminate\Database\Eloquent\Model{

    protected $table = 'cart_product_list_items';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = array('product_list_id', 'product_id');

    public function productList() {
        return $this->belongsTo('Bonweb\Laracart\ProductList', 'product_list_id', 'id');
    }

    public function product() {
        return $this->hasOne('Bonweb\Laracart\Product', 'id', 'product_id');
    }

}