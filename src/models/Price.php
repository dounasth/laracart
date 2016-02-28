<?php

namespace Bonweb\Laracart;

/**
 * Price
 *
 * @property integer $id 
 * @property integer $product_id 
 * @property string $price
 * @property float $list_price
 * @property-read \Product $product 
 * @method static \Illuminate\Database\Query\Builder|\Price whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Price whereProductId($value)
 * @method static \Illuminate\Database\Query\Builder|\Price whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\Price wherePrice($value)
 */
class Price extends \Illuminate\Database\Eloquent\Model{

    const LIST_PRICE = 'ListPrice';
    const PRICE = 'Price';

    protected $table = 'cart_product_prices';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = array('product_id', 'type', 'price');

    public function product() {
        return $this->belongsTo('Bonweb\Laracart\Product', 'product_id', 'id');
    }

    public function discount() {
        return (1- round(($this->price / $this->list_price), 2)) * 100;
    }

}