<?php

namespace Bonweb\Laracart;

class ProductList extends \Illuminate\Database\Eloquent\Model{

    protected $table = 'cart_product_list';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = array('user_id', 'name');

    public function user() {
        return $this->belongsTo('User', 'user_id', 'id');
    }

    public function products() {
        return $this->hasMany('Bonweb\Laracart\ProductListItem', 'product_list_id', 'id');
    }

    public static function forUser() {
        if (!\Auth::guest()) {
            $lists = self::whereUserId(\Auth::getUser()->id)->get();
            return $lists;
        }
        else return false;
    }

}