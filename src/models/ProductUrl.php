<?php

namespace Bonweb\Laracart;

class ProductUrl extends \Illuminate\Database\Eloquent\Model{

    protected $table = 'cart_product_aff_urls';
    protected $primaryKey = 'product_id';
    public $timestamps = false;

    protected $fillable = array('product_id', 'url');

    public function product() {
        return $this->belongsTo('Bonweb\Laracart\Product', 'product_id', 'id');
    }

}