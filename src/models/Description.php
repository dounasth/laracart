<?php

namespace Bonweb\Laracart;

/**
 * Description
 *
 * @property integer $id 
 * @property integer $product_id 
 * @property string $short
 * @property string $full
 * @property-read \Bonweb\Laracart\Product $product 
 * @method static \Illuminate\Database\Query\Builder|\Description whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Description whereProductId($value)
 * @method static \Illuminate\Database\Query\Builder|\Description whereShort($value)
 * @method static \Illuminate\Database\Query\Builder|\Description whereFull($value)
 */
class Description extends \Illuminate\Database\Eloquent\Model{

    protected $table = 'cart_product_descriptions';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = array('product_id', 'short', 'full');

    public function product() {
        return $this->belongsTo('Bonweb\Laracart\Product', 'product_id', 'id');
    }

/*    public function setTitle($title){
        $this->title = $title;
    }
    public function setShort($description){
        $this->short = $description;
    }
    public function setFull($description){
        $this->full = $description;
    }*/

} 